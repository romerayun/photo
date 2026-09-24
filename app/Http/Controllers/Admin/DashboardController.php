<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Package;
use App\Models\Photo;
use App\Models\Series;
use App\Models\Setting;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_series' => Series::count(),
            'published_series' => Series::where('is_published', true)->count(),
            'demo_series' => Series::where('is_demo', true)->count(),
            'real_series' => Series::where('is_demo', false)->count(),
            'total_photos' => Photo::count(),
            'total_packages' => Package::count(),
            'total_categories' => Category::count(),
        ];

        $recentSeries = Series::with(['category', 'photos'])
            ->latest()
            ->take(5)
            ->get();

        $isDemo = Setting::isDemoMode();
        $hasTelegram = Setting::hasTelegram();
        $hasPhone = Setting::hasPhone();

        return view('admin.dashboard', [
            'stats' => $stats,
            'recentSeries' => $recentSeries,
            'isDemo' => $isDemo,
            'hasTelegram' => $hasTelegram,
            'hasPhone' => $hasPhone,
        ]);
    }
}
