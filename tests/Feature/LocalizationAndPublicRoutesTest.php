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

    public function test_root_redirects_to_russian_locale(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/ru');
    }

    public function test_russian_home_page_renders_with_editorial_elements(): void
    {
        $response = $this->get('/ru');
        $response->assertStatus(200);
        $response->assertSee('Роман Юн');
        $response->assertSee('Фотограф в Иркутске');
        $response->assertSee('Ваши истории. Мой взгляд.');
        $response->assertSee('Обсудить съёмку');
        $response->assertSee('Смотреть работы');
    }

    public function test_english_home_page_renders_with_natural_english(): void
    {
        $response = $this->get('/en');
        $response->assertStatus(200);
        $response->assertSee('Roman Yun');
        $response->assertSee('Photographer in Irkutsk');
        $response->assertSee('Your stories. My perspective.');
        $response->assertSee('Book a Session');
        $response->assertSee('View Work');
    }

    public function test_portfolio_page_with_category_filtering(): void
    {
        $response = $this->get('/ru/portfolio');
        $response->assertStatus(200);
        $response->assertSee('Портфолио и серии');
        $response->assertSee('Все направления');

        // Filter by portraits
        $responsePortraits = $this->get('/ru/portfolio?category=portraits');
        $responsePortraits->assertStatus(200);
        $responsePortraits->assertSee('Северный свет');
    }

    public function test_series_detail_page_loads_with_photos(): void
    {
        $series = Series::where('slug', 'northern-light-portraits')->first();
        $this->assertNotNull($series);

        $response = $this->get('/ru/series/' . $series->slug);
        $response->assertStatus(200);
        $response->assertSee('Северный свет');
        $response->assertSee('Иркутск, исторический центр');
        $response->assertSee('Хочу обсудить похожую съёмку');
    }

    public function test_pricing_page_shows_packages_without_invented_prices(): void
    {
        $response = $this->get('/ru/pricing');
        $response->assertStatus(200);
        $response->assertSee('Короткая съёмка');
        $response->assertSee('Индивидуальная история');
        $response->assertSee('Событие / проект');
        $response->assertSee('Стоимость уточняется');

        // English pricing keeps RUB
        $package = Package::first();
        $package->update(['price' => 15000]);

        $responseEn = $this->get('/en/pricing');
        $responseEn->assertStatus(200);
        $responseEn->assertSee('15,000 RUB');
    }

    public function test_about_and_contacts_pages_render_cleanly(): void
    {
        $responseAbout = $this->get('/ru/about');
        $responseAbout->assertStatus(200);
        $responseAbout->assertSee('Роман Юн');

        $responseContacts = $this->get('/ru/contacts');
        $responseContacts->assertStatus(200);
        $responseContacts->assertSee('Связаться и обсудить съёмку');
    }

    public function test_seo_sitemap_and_robots(): void
    {
        $responseSitemap = $this->get('/sitemap.xml');
        $responseSitemap->assertStatus(200);
        $responseSitemap->assertHeader('Content-Type', 'application/xml');

        $responseRobots = $this->get('/robots.txt');
        $responseRobots->assertStatus(200);
        // In demo mode robots disallows crawling
        $responseRobots->assertSee('Disallow: /');
    }
}
