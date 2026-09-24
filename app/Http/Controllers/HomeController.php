<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Package;
use App\Models\Series;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(Request $request): View
    {
        $locale = 'ru';
        $isDemo = Setting::isDemoMode();

        $featuredSeries = Series::query()
            ->visible($isDemo)
            ->where('is_featured', true)
            ->with(['category', 'photos'])
            ->orderBy('sort_order')
            ->take(4)
            ->get();

        // If no featured, take visible series
        if ($featuredSeries->isEmpty()) {
            $featuredSeries = Series::query()
                ->visible($isDemo)
                ->with(['category', 'photos'])
                ->orderBy('sort_order')
                ->take(4)
                ->get();
        }

        $categories = Category::query()
            ->orderBy('sort_order')
            ->withCount(['series' => function ($q) use ($isDemo) {
                $q->visible($isDemo);
            }])
            ->get();

        $packages = Package::query()
            ->published()
            ->take(3)
            ->get();

        return view('pages.home', [
            'featuredSeries' => $featuredSeries,
            'categories' => $categories,
            'packages' => $packages,
            'isDemo' => $isDemo,
            'locale' => $locale,
        ]);
    }
}
