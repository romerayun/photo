<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Photo;
use App\Models\Series;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminAccessAndPublishingTest extends TestCase
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

    public function test_guest_cannot_access_admin_dashboard(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_login_and_view_dashboard(): void
    {
        $response = $this->post('/admin/login', [
            'email' => 'admin@romanyun.ru',
            'password' => 'SecretPassword123!',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticatedAs($this->admin);

        $dashboardResponse = $this->actingAs($this->admin)->get('/admin');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Панель управления');
    }

    public function test_admin_can_create_series(): void
    {
        $category = Category::first();

        $response = $this->actingAs($this->admin)->post('/admin/series', [
            'title_ru' => 'Новая тестовая серия',
            'title_en' => 'New Test Series',
            'category_id' => $category ? $category->id : null,
            'description_ru' => 'Тестовое описание',
            'description_en' => 'Test description',
            'location_ru' => 'Иркутск',
            'location_en' => 'Irkutsk',
            'shooting_date' => '2026',
            'is_published' => true,
            'is_featured' => true,
            'is_demo' => false,
            'sort_order' => 1,
        ]);

        $series = Series::where('title_ru', 'Новая тестовая серия')->first();
        $this->assertNotNull($series);
        $response->assertRedirect(route('admin.series.edit', $series));
        $this->assertEquals('new-test-series', $series->slug);
    }

    public function test_admin_can_upload_photo_and_set_cover(): void
    {
        Storage::fake('public');

        $series = Series::create([
            'slug' => 'test-upload-series',
            'title_ru' => 'Серия для загрузки',
            'is_published' => true,
            'is_demo' => false,
        ]);

        $fakeImage = UploadedFile::fake()->image('test-frame.jpg', 1200, 800);

        $response = $this->actingAs($this->admin)->post("/admin/series/{$series->id}/photos", [
            'photos' => [$fakeImage],
        ]);

        $response->assertSessionHas('success');

        $photo = Photo::where('series_id', $series->id)->first();
        $this->assertNotNull($photo);
        Storage::disk('public')->assertExists($photo->image_path);

        // Auto cover assignment
        $series->refresh();
        $this->assertEquals($photo->image_path, $series->cover_image);
    }

    public function test_public_mode_hides_demo_series_when_demo_mode_is_off(): void
    {
        // Series with demo flag
        $demoSeries = Series::where('is_demo', true)->first();
        $this->assertNotNull($demoSeries);

        // Turn demo mode off
        Setting::set('demo_mode', '0');

        $response = $this->get('/portfolio');
        $response->assertStatus(200);
        // The demo series shouldn't be listed when demo mode is disabled
        $response->assertDontSee($demoSeries->title_ru);
    }

    public function test_make_admin_artisan_command_works(): void
    {
        $this->artisan('app:create-admin', [
            '--email' => 'newowner@example.com',
            '--name' => 'Роман',
        ])
        ->expectsQuestion('Enter password (min 8 characters)', 'NewSecurePassword123!')
        ->expectsQuestion('Confirm password', 'NewSecurePassword123!')
        ->assertExitCode(0);

        $this->assertDatabaseHas('users', ['email' => 'newowner@example.com']);
    }
}
