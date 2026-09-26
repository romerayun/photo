<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Package;
use App\Models\Series;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a specific category service page with Quill description and series/works grid.
     */
    public function show(Request $request, string $slug): View|JsonResponse
    {
        $locale = 'ru';
        $isDemo = Setting::isDemoMode();

        $category = Category::where('slug', $slug)->firstOrFail();

        // Get series belonging to this category
        $seriesQuery = Series::query()
            ->visible($isDemo)
            ->where('category_id', $category->id)
            ->with(['category', 'photos'])
            ->orderBy('sort_order');

        $seriesList = $seriesQuery->paginate(6)->withQueryString();

        // AJAX pagination support
        if ($request->ajax() || $request->wantsJson() || $request->has('ajax')) {
            $html = view('pages.partials.series-cards', [
                'seriesList' => $seriesList,
                'locale' => $locale,
            ])->render();

            return response()->json([
                'html' => $html,
                'hasMore' => $seriesList->hasMorePages(),
                'nextPage' => $seriesList->currentPage() + 1,
                'total' => $seriesList->total(),
            ]);
        }

        // Other categories for easy navigation
        $otherCategories = Category::where('id', '!=', $category->id)
            ->orderBy('sort_order')
            ->get();

        // Packages for CTA / pricing section
        $packages = Package::query()->published()->take(3)->get();

        return view('pages.category-show', [
            'category' => $category,
            'seriesList' => $seriesList,
            'otherCategories' => $otherCategories,
            'packages' => $packages,
            'locale' => $locale,
            'isDemo' => $isDemo,
        ]);
    }
}
