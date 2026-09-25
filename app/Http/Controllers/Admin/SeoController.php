<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoMeta;
use App\Services\SeoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SeoController extends Controller
{
    /**
     * List all SEO metadata records with filters and quick search.
     */
    public function index(Request $request): View
    {
        // Automatically make sure all pages exist
        SeoService::syncAllPages();

        $query = SeoMeta::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('path', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            if ($type === 'static') {
                $staticPaths = array_keys(SeoService::staticPages());
                $query->whereIn('path', $staticPaths);
            } elseif ($type === 'series') {
                $query->where('path', 'like', '/series/%');
            } elseif ($type === 'articles') {
                $query->where('path', 'like', '/articles/%');
            }
        }

        $metas = $query->orderBy('path')->paginate(20)->withQueryString();

        $stats = [
            'total' => SeoMeta::count(),
            'series' => SeoMeta::where('path', 'like', '/series/%')->count(),
            'articles' => SeoMeta::where('path', 'like', '/articles/%')->count(),
            'static' => SeoMeta::whereIn('path', array_keys(SeoService::staticPages()))->count(),
        ];

        return view('admin.seo.index', [
            'metas' => $metas,
            'stats' => $stats,
            'currentSearch' => $search,
            'currentType' => $type,
        ]);
    }

    /**
     * Show form to edit SEO settings for a specific page path.
     */
    public function edit(SeoMeta $seo): View
    {
        return view('admin.seo.edit', [
            'seo' => $seo,
        ]);
    }

    /**
     * Update SEO metadata for a page.
     */
    public function update(Request $request, SeoMeta $seo): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'string', 'max:500'],
            'canonical' => ['nullable', 'url', 'max:255'],
            'robots' => ['required', 'string', 'max:50'],
        ]);

        // When user edits in admin, it's no longer purely auto-generated
        $validated['is_auto_generated'] = false;

        $seo->update($validated);

        return redirect()->route('admin.seo.index')->with('success', "SEO настройки для страницы «{$seo->path}» успешно сохранены.");
    }

    /**
     * Force synchronization of all site pages.
     */
    public function sync(): RedirectResponse
    {
        $result = SeoService::syncAllPages();

        return redirect()->route('admin.seo.index')->with('success', "Синхронизация завершена: всего страниц в каталоге SEO — {$result['total']}. Новые страницы автоматически добавлены.");
    }

    /**
     * Reset a page to auto-generated default values.
     */
    public function reset(SeoMeta $seo): RedirectResponse
    {
        $seo->delete();
        SeoService::syncAllPages();

        return redirect()->route('admin.seo.index')->with('success', "SEO настройки для страницы «{$seo->path}» сброшены к значениям по умолчанию.");
    }
}
