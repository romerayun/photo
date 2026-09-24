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

    public function test_portfolio_page_with_category_filtering(): void
    {
        $response = $this->get('/portfolio');
        $response->assertStatus(200);
        $response->assertSee('Портфолио и серии');
        $response->assertSee('Все направления');

        // Filter by portraits
        $responsePortraits = $this->get('/portfolio?category=portraits');
        $responsePortraits->assertStatus(200);
        $responsePortraits->assertSee('Северный свет');
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
        $responseRobots->assertSee('Disallow: /');
    }
}
