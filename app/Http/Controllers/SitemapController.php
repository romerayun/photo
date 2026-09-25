<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Package;
use App\Models\Series;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SitemapController extends Controller
{
    /**
     * Render the human-friendly HTML Sitemap page.
     */
    public function html(Request $request): View
    {
        $locale = 'ru';
        $isDemo = Setting::isDemoMode();

        $categories = Category::query()
            ->orderBy('sort_order')
            ->get();

        $seriesList = Series::query()
            ->visible($isDemo)
            ->with(['category', 'photos'])
            ->orderBy('sort_order')
            ->get();

        $articles = Article::query()
            ->published()
            ->latest('published_at')
            ->get();

        $packages = Package::query()
            ->published()
            ->get();

        return view('pages.sitemap', [
            'categories' => $categories,
            'seriesList' => $seriesList,
            'articles' => $articles,
            'packages' => $packages,
            'locale' => $locale,
        ]);
    }

    /**
     * Render the XML Sitemap for search engines.
     */
    public function xml(): Response
    {
        $isDemo = Setting::isDemoMode();

        $staticPages = [
            'home' => '1.0',
            'portfolio.index' => '0.9',
            'articles.index' => '0.8',
            'pricing.index' => '0.8',
            'about.index' => '0.8',
            'contacts.index' => '0.8',
            'sitemap.page' => '0.6',
        ];

        $series = Series::query()
            ->visible($isDemo)
            ->get();

        $articles = Article::query()
            ->published()
            ->get();

        $urls = [];

        foreach ($staticPages as $routeName => $priority) {
            $urls[] = [
                'loc' => route($routeName),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => $priority,
            ];
        }

        foreach ($series as $s) {
            $urls[] = [
                'loc' => route('series.show', ['slug' => $s->slug]),
                'lastmod' => $s->updated_at?->toDateString() ?? now()->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }

        foreach ($articles as $art) {
            $urls[] = [
                'loc' => route('articles.show', ['slug' => $art->slug]),
                'lastmod' => $art->updated_at?->toDateString() ?? now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.7',
            ];
        }

        $xml = view('seo.sitemap', ['urls' => $urls])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * Backward-compatible alias for xml().
     */
    public function index(): Response
    {
        return $this->xml();
    }
}
