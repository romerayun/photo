<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Package;
use App\Models\Photo;
use App\Models\Series;
use App\Models\Setting;
use App\Models\Shoot;
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
            'total_articles' => Article::count(),
            'published_articles' => Article::where('is_published', true)->count(),
            'total_comments' => Comment::count(),
            'total_photos' => Photo::count(),
            'total_packages' => Package::count(),
            'total_categories' => Category::count(),
            'total_shoots' => Shoot::count(),
            'upcoming_shoots' => Shoot::upcoming()->count(),
        ];

        $upcomingShoots = Shoot::upcoming()->take(5)->get();

        $recentSeries = Series::with(['category', 'photos'])
            ->latest()
            ->take(5)
            ->get();

        $recentArticles = Article::withCount('comments')
            ->latest()
            ->take(5)
            ->get();

        $isDemo = Setting::isDemoMode();
        $hasTelegram = Setting::hasTelegram();
        $hasPhone = Setting::hasPhone();

        return view('admin.dashboard', [
            'stats' => $stats,
            'upcomingShoots' => $upcomingShoots,
            'recentSeries' => $recentSeries,
            'recentArticles' => $recentArticles,
            'isDemo' => $isDemo,
            'hasTelegram' => $hasTelegram,
            'hasPhone' => $hasPhone,
        ]);
    }
}
