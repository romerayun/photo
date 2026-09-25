<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Series;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PortfolioController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $locale = 'ru';
        $isDemo = Setting::isDemoMode();

        $query = Series::query()
            ->visible($isDemo)
            ->with(['category', 'photos'])
            ->orderBy('sort_order');

        $seriesList = $query->paginate(6)->withQueryString();

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

        return view('pages.portfolio', [
            'seriesList' => $seriesList,
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
