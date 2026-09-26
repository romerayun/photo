<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Photo;
use App\Models\Series;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CategoryServicePagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_page_renders_with_description_and_series(): void
    {
        $category = Category::create([
            'slug' => 'portraits',
            'name_ru' => 'Портретная съёмка',
            'description_ru' => 'Индивидуальные фотосессии в студии и городе',
            'content' => '<h2>Особенности портрета</h2><p>Помогу раскрыться перед камерой и подскажу удачные ракурсы.</p>',
            'meta_title' => 'Портретный фотограф Роман Юн в Иркутске',
            'meta_description' => 'Индивидуальные портреты от фотографа Романа Юна в Иркутске.',
        ]);

        $series = Series::create([
            'slug' => 'anna-portrait',
            'title_ru' => 'Анна в лучах заката',
            'description_ru' => 'Летняя портретная серия на набережной',
            'category_id' => $category->id,
            'is_published' => true,
            'sort_order' => 1,
        ]);

        $response = $this->get('/category/portraits');

        $response->assertStatus(200);
        $response->assertSee('Портретная съёмка');
        $response->assertSee('Особенности портрета');
        $response->assertSee('Помогу раскрыться перед камерой');
        $response->assertSee('Анна в лучах заката');
        $response->assertSee('РАБОТЫ: Портретная съёмка');
    }

    public function test_admin_can_edit_category_with_quill_content(): void
    {
        $admin = User::factory()->create();
        $category = Category::create([
            'slug' => 'couples',
            'name_ru' => 'Для двоих (Love Story)',
            'description_ru' => 'Романтические истории',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.categories.edit', $category))
            ->assertStatus(200)
            ->assertSee('Редактировать категорию')
            ->assertSee('editor-container');

        $this->actingAs($admin)
            ->put(route('admin.categories.update', $category), [
                'name_ru' => 'Love Story и съёмка пар',
                'slug' => 'couples',
                'description_ru' => 'Искренние кадры для двоих',
                'content' => '<h3>Как подготовиться паре</h3><p>Выберите гармоничные образы в нейтральных тонах.</p>',
                'meta_title' => 'Love Story фотограф Роман Юн',
                'meta_description' => 'Фотосессии для пар в Иркутске',
            ])
            ->assertRedirect();

        $category->refresh();
        $this->assertEquals('Love Story и съёмка пар', $category->name_ru);
        $this->assertStringContainsString('Как подготовиться паре', $category->content);
        $this->assertStringContainsString('Выберите гармоничные образы', $category->content);
    }
}
