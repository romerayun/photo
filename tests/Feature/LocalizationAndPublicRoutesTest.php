<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Package;
use App\Models\Series;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationAndPublicRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_root_renders_russian_home_page(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Роман Юн');
        $response->assertSee('Фотограф в Иркутске');
        $response->assertSee('Обсудить съёмку');
        $response->assertSee('Смотреть работы');
    }

    public function test_legacy_localized_urls_redirect_to_clean_routes(): void
    {
        $this->get('/ru')->assertRedirect('/');
        $this->get('/en')->assertRedirect('/');
        $this->get('/ru/portfolio')->assertRedirect('/portfolio');
        $this->get('/en/pricing')->assertRedirect('/pricing');
    }

    public function test_portfolio_page_with_load_more_pagination(): void
    {
        // Seed 2 extra series so total is 7 (> 6)
        Series::create([
            'slug' => 'test-extra-series-1',
            'title_ru' => 'Тест Серия 1',
            'title_en' => 'Test Series 1',
            'is_published' => true,
            'is_demo' => true,
            'sort_order' => 10,
        ]);
        Series::create([
            'slug' => 'test-extra-series-2',
            'title_ru' => 'Тест Серия 2',
            'title_en' => 'Test Series 2',
            'is_published' => true,
            'is_demo' => true,
            'sort_order' => 11,
        ]);

        $response = $this->get('/portfolio');
        $response->assertStatus(200);
        $response->assertSee('ПОРТФОЛИО');
        $response->assertSee('Загрузить ещё');
        $response->assertDontSee('Все направления');

        // Ajax load more request (page 1 - initial)
        $ajaxPage1 = $this->getJson('/portfolio?page=1');
        $ajaxPage1->assertStatus(200);
        $ajaxPage1->assertJsonStructure([
            'html',
            'hasMore',
            'nextPage',
            'total',
        ]);
        $this->assertTrue($ajaxPage1->json('hasMore'));
        $this->assertEquals(2, $ajaxPage1->json('nextPage'));

        // Ajax load more request (page 2 - last)
        $ajaxPage2 = $this->getJson('/portfolio?page=2');
        $ajaxPage2->assertStatus(200);
        $this->assertFalse($ajaxPage2->json('hasMore'));
    }

    public function test_series_detail_page_loads_with_photos(): void
    {
        $series = Series::where('slug', 'northern-light-portraits')->first();
        $this->assertNotNull($series);

        $response = $this->get('/series/' . $series->slug);
        $response->assertStatus(200);
        $response->assertSee('Северный свет');
        $response->assertSee('Иркутск, исторический центр');
        $response->assertSee('Хочу обсудить похожую съёмку');
    }

    public function test_pricing_page_shows_packages_without_invented_prices(): void
    {
        $response = $this->get('/pricing');
        $response->assertStatus(200);
        $response->assertSee('Короткая съёмка');
        $response->assertSee('Индивидуальная история');
        $response->assertSee('Событие / проект');
        $response->assertSee('Стоимость уточняется');
    }

    public function test_about_and_contacts_pages_render_cleanly(): void
    {
        $responseAbout = $this->get('/about');
        $responseAbout->assertStatus(200);
        $responseAbout->assertSee('Роман Юн');

        $responseContacts = $this->get('/contacts');
        $responseContacts->assertStatus(200);
        $responseContacts->assertSee('Связаться и обсудить съёмку');
    }

    public function test_seo_sitemap_and_robots(): void
    {
        $responseSitemap = $this->get('/sitemap.xml');
        $responseSitemap->assertStatus(200);
        $responseSitemap->assertHeader('Content-Type', 'application/xml');
        $responseSitemap->assertDontSee('/en');

        $responseRobots = $this->get('/robots.txt');
        $responseRobots->assertStatus(200);
        $responseRobots->assertSee("Allow: /\n");
        $responseRobots->assertSee("Disallow: /admin");
        $responseRobots->assertDontSee("Disallow: /\n");

        // Public pages should have index, follow
        $responseHome = $this->get('/');
        $responseHome->assertStatus(200);
        $responseHome->assertSee('<meta name="robots" content="index, follow">', false);

        // Admin login page should have noindex, nofollow
        $responseLogin = $this->get('/admin/login');
        $responseLogin->assertStatus(200);
        $responseLogin->assertSee('<meta name="robots" content="noindex, nofollow">', false);
    }
}
