<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PackageController as AdminPackageController;
use App\Http\Controllers\Admin\PhotoController as AdminPhotoController;
use App\Http\Controllers\Admin\SeriesController as AdminSeriesController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// SEO routes
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');

// Public routes (Russian only)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('/series/{slug}', [PortfolioController::class, 'showSeries'])->name('series.show');
Route::get('/pricing', [PricingController::class, 'index'])->name('pricing.index');
Route::get('/about', [AboutController::class, 'index'])->name('about.index');
Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');

// Redirect legacy /ru and /en paths
Route::redirect('/ru', '/', 301);
Route::redirect('/en', '/', 301);
Route::get('/ru/{any}', function ($any) {
    return redirect('/' . $any, 301);
})->where('any', '.*');
Route::get('/en/{any}', function ($any) {
    return redirect('/' . $any, 301);
})->where('any', '.*');

// Admin authentication
Route::get('/admin/login', [AdminAuthController::class, 'showLogin'])->name('login');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Protected admin routes
    Route::middleware('auth')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Series
        Route::resource('series', AdminSeriesController::class)->except(['show']);
        Route::post('/series/{series}/photos', [AdminPhotoController::class, 'store'])->name('series.photos.store');
        Route::post('/series/{series}/photos/reorder', [AdminPhotoController::class, 'reorder'])->name('series.photos.reorder');
        Route::post('/photos/{photo}/cover', [AdminPhotoController::class, 'setCover'])->name('photos.cover');
        Route::delete('/photos/{photo}', [AdminPhotoController::class, 'destroy'])->name('photos.destroy');

        // Categories
        Route::resource('categories', AdminCategoryController::class)->except(['create', 'show', 'edit']);

        // Packages
        Route::get('/packages', [AdminPackageController::class, 'index'])->name('packages.index');
        Route::get('/packages/{package}/edit', [AdminPackageController::class, 'edit'])->name('packages.edit');
        Route::put('/packages/{package}', [AdminPackageController::class, 'update'])->name('packages.update');

        // Settings
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');
    });
});
