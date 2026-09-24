<?php

namespace App\Http\Controllers;

use App\Models\Series;
use App\Models\Setting;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $isDemo = Setting::isDemoMode();

        $staticPages = [
            'home',
            'portfolio.index',
            'pricing.index',
            'about.index',
            'contacts.index',
        ];

        $series = Series::query()
            ->visible($isDemo)
            ->get();

        $urls = [];

        foreach ($staticPages as $routeName) {
            $urls[] = [
                'loc' => route($routeName),
                'lastmod' => now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => $routeName === 'home' ? '1.0' : '0.8',
            ];
        }

        foreach ($series as $s) {
            $urls[] = [
                'loc' => route('series.show', ['slug' => $s->slug]),
                'lastmod' => $s->updated_at->toDateString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }

        $xml = view('seo.sitemap', ['urls' => $urls])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
