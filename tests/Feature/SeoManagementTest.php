<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\SeoMeta;
use App\Models\Series;
use App\Models\User;
use App\Services\SeoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_all_pages_automatically_synced_to_seo_metas(): void
    {
        SeoService::syncAllPages();

        $this->assertDatabaseHas('seo_metas', ['path' => '/']);
        $this->assertDatabaseHas('seo_metas', ['path' => '/portfolio']);
        $this->assertDatabaseHas('seo_metas', ['path' => '/articles']);
        $this->assertDatabaseHas('seo_metas', ['path' => '/pricing']);
        $this->assertDatabaseHas('seo_metas', ['path' => '/about']);
        $this->assertDatabaseHas('seo_metas', ['path' => '/contacts']);
    }

    public function test_creating_series_automatically_adds_seo_entry(): void
    {
        $series = Series::create([
            'title_ru' => 'Новая серия на Ольхоне',
            'slug' => 'novaya-seriya-olkhon',
            'description_ru' => 'Красивая осенняя съёмка на скале Шаманка',
            'is_published' => true,
        ]);

        $this->assertDatabaseHas('seo_metas', [
            'path' => '/series/novaya-seriya-olkhon',
            'title' => 'Новая серия на Ольхоне — Роман Юн',
            'description' => 'Красивая осенняя съёмка на скале Шаманка',
        ]);
    }

    public function test_creating_article_automatically_adds_seo_entry(): void
    {
        $article = Article::create([
            'title' => 'Советы по подбору гардероба для фотосессии',
            'slug' => 'sovety-po-podboru-garderoba',
            'content' => '<p>Полный гайд по цветам и фасонам одежды.</p>',
            'excerpt' => 'Полный гайд по цветам и фасонам одежды.',
            'is_published' => true,
            'reading_time' => 5,
        ]);

        $this->assertDatabaseHas('seo_metas', [
            'path' => '/articles/sovety-po-podboru-garderoba',
            'title' => 'Советы по подбору гардероба для фотосессии — Роман Юн',
        ]);
    }

    public function test_admin_can_access_seo_management_and_edit_page_meta(): void
    {
        $admin = User::create([
            'name' => 'Роман Юн',
            'email' => 'admin@romanyun.ru',
            'password' => \Illuminate\Support\Facades\Hash::make('SecretPassword123!'),
        ]);

        SeoService::syncAllPages();

        $response = $this->actingAs($admin)->get('/admin/seo');
        $response->assertStatus(200);
        $response->assertSee('SEO настройки страниц');

        $seo = SeoMeta::where('path', '/portfolio')->first();
        $this->assertNotNull($seo);

        $updateResponse = $this->actingAs($admin)->put("/admin/seo/{$seo->id}", [
            'title' => 'Кастомный заголовок портфолио для SEO',
            'description' => 'Кастомное уникальное описание для поисковиков',
            'og_title' => 'Портфолио в соцсетях',
            'og_description' => 'Описание для соцсетей',
            'canonical' => 'http://localhost/portfolio',
            'robots' => 'index, follow',
        ]);

        $updateResponse->assertRedirect('/admin/seo');

        $this->assertDatabaseHas('seo_metas', [
            'path' => '/portfolio',
            'title' => 'Кастомный заголовок портфолио для SEO',
            'is_auto_generated' => false,
        ]);

        // When visiting the public page, custom meta tags are used
        $pageResponse = $this->get('/portfolio');
        $pageResponse->assertStatus(200);
        $pageResponse->assertSee('<title>Кастомный заголовок портфолио для SEO</title>', false);
        $pageResponse->assertSee('<meta name="description" content="Кастомное уникальное описание для поисковиков">', false);
    }
}
