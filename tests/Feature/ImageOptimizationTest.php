<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Photo;
use App\Models\Series;
use App\Models\User;
use App\Services\ImageOptimizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageOptimizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::create([
            'name' => 'Роман Юн',
            'email' => 'admin@romanyun.ru',
            'password' => Hash::make('SecretPassword123!'),
        ]);
    }

    public function test_oversized_photos_are_compressed_and_scaled_down_for_seo(): void
    {
        Storage::fake('public');

        $series = Series::create([
            'slug' => 'irkutsk-street-portrait',
            'title_ru' => 'Уличный портрет в Иркутске',
            'is_published' => true,
        ]);

        // Create an oversized 3600x2400 fake image (like from professional DSLR)
        $hugeImage = UploadedFile::fake()->image('DSC_9823.JPG', 3600, 2400);

        $response = $this->actingAs($this->admin)->post("/admin/series/{$series->id}/photos", [
            'photos' => [$hugeImage],
        ]);

        $response->assertSessionHas('success');

        $photo = Photo::where('series_id', $series->id)->first();
        $this->assertNotNull($photo);

        // Check storage existence
        Storage::disk('public')->assertExists($photo->image_path);

        // Longest dimension must not exceed MAX_SERIES_DIMENSION (2560)
        $this->assertLessThanOrEqual(ImageOptimizer::MAX_SERIES_DIMENSION, $photo->width);
        $this->assertLessThanOrEqual(ImageOptimizer::MAX_SERIES_DIMENSION, $photo->height);

        // Check that aspect ratio is preserved (3600 / 2400 = 1.5)
        $this->assertEquals(2560, $photo->width);
        $this->assertEquals(1707, $photo->height);

        // Check semantic filename contains series slug for Google Images SEO
        $this->assertStringContainsString('irkutsk-street-portrait', $photo->image_path);
        $this->assertStringEndsWith('.jpg', $photo->image_path);
    }

    public function test_category_images_are_compressed_and_optimized(): void
    {
        Storage::fake('public');

        $largeCover = UploadedFile::fake()->image('RAW_COVER.JPG', 3000, 2000);

        $response = $this->actingAs($this->admin)->post('/admin/categories', [
            'name_ru' => 'Контент для брендов',
            'slug' => 'brand-content',
            'description_ru' => 'Съемки для экспертов и бизнеса',
            'image' => $largeCover,
            'sort_order' => 1,
        ]);

        $response->assertSessionHas('success');

        $category = Category::where('slug', 'brand-content')->first();
        $this->assertNotNull($category);
        $this->assertNotNull($category->image);

        Storage::disk('public')->assertExists($category->image);

        // Check semantic filename contains category slug
        $this->assertStringContainsString('brand-content', $category->image);

        // Verify stored file dimensions are constrained to max category dimension
        $fullPath = Storage::disk('public')->path($category->image);
        $info = getimagesize($fullPath);
        $this->assertNotFalse($info);
        $this->assertLessThanOrEqual(ImageOptimizer::MAX_CATEGORY_DIMENSION, $info[0]);
    }

    public function test_smaller_images_retain_original_resolution_without_upscaling(): void
    {
        Storage::fake('public');

        $optimizer = new ImageOptimizer();
        $smallFile = UploadedFile::fake()->image('small-photo.jpg', 1200, 800);

        $result = $optimizer->optimizeAndStore($smallFile, 'test', 'small-photo');

        $this->assertEquals(1200, $result['width']);
        $this->assertEquals(800, $result['height']);
        Storage::disk('public')->assertExists($result['path']);
    }

    public function test_max_upload_size_validation_allows_up_to_50mb_and_rejects_larger(): void
    {
        Storage::fake('public');

        $series = Series::create([
            'slug' => 'test-50mb-series',
            'title_ru' => 'Серия 50 МБ',
            'is_published' => true,
        ]);

        // File within 50 MB limit (e.g. 20 MB = 20480 KB)
        $validFile = UploadedFile::fake()->image('within-limit.jpg')->size(20480);
        $validResponse = $this->actingAs($this->admin)->post("/admin/series/{$series->id}/photos", [
            'photos' => [$validFile],
        ]);
        $validResponse->assertSessionHasNoErrors();

        // File exceeding 50 MB limit (e.g. 52 MB = 53248 KB)
        $oversizedFile = UploadedFile::fake()->image('too-large.jpg')->size(53248);
        $oversizedResponse = $this->actingAs($this->admin)->post("/admin/series/{$series->id}/photos", [
            'photos' => [$oversizedFile],
        ]);
        $oversizedResponse->assertSessionHasErrors(['photos.0']);
    }
}
