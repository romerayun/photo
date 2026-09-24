<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Series;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(Request $request): View
    {
        $locale = 'ru';
        $isDemo = Setting::isDemoMode();
        $selectedCategorySlug = $request->query('category');

        $categories = Category::query()
            ->orderBy('sort_order')
            ->withCount(['series' => function ($q) use ($isDemo) {
                $q->visible($isDemo);
            }])
            ->get();

        $selectedCategory = null;
        if ($selectedCategorySlug) {
            $selectedCategory = $categories->firstWhere('slug', $selectedCategorySlug);
        }

        $query = Series::query()
            ->visible($isDemo)
            ->with(['category', 'photos'])
            ->orderBy('sort_order');

        if ($selectedCategory) {
            $query->where('category_id', $selectedCategory->id);
        }

        $seriesList = $query->get();

        return view('pages.portfolio', [
            'seriesList' => $seriesList,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'selectedCategorySlug' => $selectedCategorySlug,
            'isDemo' => $isDemo,
            'locale' => $locale,
        ]);
    }

    public function showSeries(Request $request, string $slug): View
    {
        $locale = 'ru';
        $isDemo = Setting::isDemoMode();

        $series = Series::query()
            ->visible($isDemo)
            ->where('slug', $slug)
            ->with(['category', 'photos'])
            ->firstOrFail();

        // Next series for carousel/continuation
        $nextSeries = Series::query()
            ->visible($isDemo)
            ->where('id', '!=', $series->id)
            ->where('sort_order', '>=', $series->sort_order)
            ->orderBy('sort_order')
            ->first();

        if (!$nextSeries) {
            $nextSeries = Series::query()
                ->visible($isDemo)
                ->where('id', '!=', $series->id)
                ->orderBy('sort_order')
                ->first();
        }

        return view('pages.series-show', [
            'series' => $series,
            'nextSeries' => $nextSeries,
            'isDemo' => $isDemo,
            'locale' => $locale,
        ]);
    }
}
