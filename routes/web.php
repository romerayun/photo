<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\ArticleController as AdminArticleController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PackageController as AdminPackageController;
use App\Http\Controllers\Admin\PhotoController as AdminPhotoController;
use App\Http\Controllers\Admin\SeoController as AdminSeoController;
use App\Http\Controllers\Admin\SeriesController as AdminSeriesController;
use App\Http\Controllers\Admin\SettingController as AdminSettingController;
use App\Http\Controllers\Admin\ShootController as AdminShootController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\PricingController;
use App\Http\Controllers\PublicShootController;
use App\Http\Controllers\RobotsController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

// SEO routes
Route::get('/sitemap', [SitemapController::class, 'html'])->name('sitemap.page');
Route::get('/sitemap.xml', [SitemapController::class, 'xml'])->name('sitemap');
Route::get('/robots.txt', [RobotsController::class, 'index'])->name('robots');

// Public routes (Russian only)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
Route::get('/series/{slug}', [PortfolioController::class, 'showSeries'])->name('series.show');
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');
Route::post('/articles/{article}/comments', [ArticleController::class, 'storeComment'])->name('articles.comments.store');
Route::get('/pricing', [PricingController::class, 'index'])->name('pricing.index');
Route::get('/about', [AboutController::class, 'index'])->name('about.index');
Route::get('/contacts', [ContactController::class, 'index'])->name('contacts.index');
Route::post('/contacts', [ContactController::class, 'send'])->name('contacts.send');

// Public photoshoot share card for clients
Route::get('/shoot/{token}', [PublicShootController::class, 'show'])->name('shoots.share.short');
Route::get('/shoots/share/{token}', [PublicShootController::class, 'show'])->name('shoots.share');
Route::get('/shoots/share/{token}/ics', [PublicShootController::class, 'ics'])->name('shoots.share.ics');
Route::post('/shoots/share/{token}/receipt', [PublicShootController::class, 'uploadReceipt'])->name('shoots.share.receipt');
Route::post('/shoots/share/{token}/max-link', [PublicShootController::class, 'generateMaxLink'])->name('shoots.share.max_link');

// MAX Messenger Webhook endpoint
Route::post('/webhook/max', [\App\Http\Controllers\MaxWebhookController::class, 'handle'])->name('webhook.max');
Route::post('/api/max/webhook', [\App\Http\Controllers\MaxWebhookController::class, 'handle']);

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

        // Articles
        Route::post('/articles/upload-image', [AdminArticleController::class, 'uploadInlineImage'])->name('articles.upload_image');
        Route::resource('articles', AdminArticleController::class)->except(['show']);
        Route::delete('/articles/{article}/photos/{image}', [AdminArticleController::class, 'destroyPhoto'])->name('articles.photos.destroy');

        // Comments moderation
        Route::delete('/comments/{comment}', [AdminCommentController::class, 'destroy'])->name('comments.destroy');
        Route::post('/comments/{comment}/toggle', [AdminCommentController::class, 'toggle'])->name('comments.toggle');

        // Categories
        Route::post('/categories/upload-image', [AdminCategoryController::class, 'uploadInlineImage'])->name('categories.upload_image');
        Route::resource('categories', AdminCategoryController::class)->except(['create', 'show']);

        // Packages
        Route::get('/packages', [AdminPackageController::class, 'index'])->name('packages.index');
        Route::get('/packages/create', [AdminPackageController::class, 'create'])->name('packages.create');
        Route::post('/packages', [AdminPackageController::class, 'store'])->name('packages.store');
        Route::get('/packages/{package}/edit', [AdminPackageController::class, 'edit'])->name('packages.edit');
        Route::put('/packages/{package}', [AdminPackageController::class, 'update'])->name('packages.update');
        Route::delete('/packages/{package}', [AdminPackageController::class, 'destroy'])->name('packages.destroy');

        // SEO Management
        Route::get('/seo', [AdminSeoController::class, 'index'])->name('seo.index');
        Route::post('/seo/sync', [AdminSeoController::class, 'sync'])->name('seo.sync');
        Route::get('/seo/{seo}/edit', [AdminSeoController::class, 'edit'])->name('seo.edit');
        Route::put('/seo/{seo}', [AdminSeoController::class, 'update'])->name('seo.update');
        Route::post('/seo/{seo}/reset', [AdminSeoController::class, 'reset'])->name('seo.reset');

        // Settings
        Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

        // Shoots & Calendar
        Route::get('/shoots', [AdminShootController::class, 'index'])->name('shoots.index');
        Route::post('/shoots', [AdminShootController::class, 'store'])->name('shoots.store');
        Route::put('/shoots/{shoot}', [AdminShootController::class, 'update'])->name('shoots.update');
        Route::patch('/shoots/{shoot}/status', [AdminShootController::class, 'updateStatus'])->name('shoots.status');
        Route::patch('/shoots/{shoot}/gallery-link', [AdminShootController::class, 'updateGalleryLink'])->name('shoots.gallery_link');
        Route::delete('/shoots/{shoot}', [AdminShootController::class, 'destroy'])->name('shoots.destroy');
        Route::post('/shoots/{shoot}/files', [AdminShootController::class, 'uploadFiles'])->name('shoots.files.upload');
        Route::delete('/shoots/files/{file}', [AdminShootController::class, 'destroyFile'])->name('shoots.files.destroy');
        Route::patch('/shoots/{shoot}/confirm-booking', [AdminShootController::class, 'confirmBooking'])->name('shoots.confirm_booking');
    });
});
