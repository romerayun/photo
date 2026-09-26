<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

use App\Services\ImageOptimizer;
use App\Services\SeoService;
use App\Models\SeoMeta;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('series')->orderBy('sort_order')->get();
        return view('admin.categories.index', ['categories' => $categories]);
    }

    public function edit(Category $category): View
    {
        return view('admin.categories.edit', ['category' => $category]);
    }

    public function store(Request $request, ImageOptimizer $optimizer): RedirectResponse
    {
        $validated = $request->validate([
            'name_ru' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
            'description_ru' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:51200'],
            'sort_order' => ['nullable', 'integer'],
        ], [
            'image.max' => 'Максимальный размер изображения: 50 МБ.',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name_ru']);
        }

        $validated['sort_order'] = (int) $request->input('sort_order', 0);

        if (!empty($validated['content'])) {
            $validated['content'] = $this->sanitizeContent($validated['content']);
        }

        if ($request->hasFile('image')) {
            $optimized = $optimizer->optimizeAndStore(
                $request->file('image'),
                'categories',
                $validated['slug'] ?: $validated['name_ru'],
                ImageOptimizer::MAX_CATEGORY_DIMENSION
            );
            $validated['image'] = $optimized['path'];
        }

        $newCategory = Category::create($validated);
        SeoService::syncCategory($newCategory);

        return back()->with('success', 'Категория успешно добавлена.');
    }

    public function update(Request $request, Category $category, ImageOptimizer $optimizer): RedirectResponse
    {
        $oldSlug = $category->slug;

        $validated = $request->validate([
            'name_ru' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug,' . $category->id],
            'description_ru' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:51200'],
            'sort_order' => ['nullable', 'integer'],
        ], [
            'image.max' => 'Максимальный размер изображения: 50 МБ.',
        ]);

        $validated['sort_order'] = (int) $request->input('sort_order', 0);

        if (!empty($validated['content'])) {
            $validated['content'] = $this->sanitizeContent($validated['content']);
        }

        if ($request->boolean('remove_image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $validated['image'] = null;
        } elseif ($request->hasFile('image')) {
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }
            $optimized = $optimizer->optimizeAndStore(
                $request->file('image'),
                'categories',
                $validated['slug'] ?: $validated['name_ru'],
                ImageOptimizer::MAX_CATEGORY_DIMENSION
            );
            $validated['image'] = $optimized['path'];
        }

        $category->update($validated);
        SeoService::syncCategory($category, $oldSlug);

        return back()->with('success', 'Категория успешно обновлена.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $path = SeoMeta::normalizePath('/category/' . $category->slug);
        SeoMeta::where('path', $path)->delete();

        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }
        $category->delete();
        return back()->with('success', 'Категория удалена.');
    }

    /**
     * Upload an inline image from Quill editor for Category description.
     */
    public function uploadInlineImage(Request $request, ImageOptimizer $optimizer)
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:20480'],
        ]);

        $optimized = $optimizer->optimizeAndStore(
            $request->file('image'),
            'categories/content',
            'cat-img-' . time(),
            ImageOptimizer::MAX_SERIES_DIMENSION
        );

        return response()->json([
            'url' => asset('storage/' . $optimized['path']),
        ]);
    }

    /**
     * Clean pasted rich text content from foreign inline styles, font families, and fixed colors.
     */
    protected function sanitizeContent(string $html): string
    {
        // Remove style tags and script tags completely
        $clean = preg_replace('#<(script|style|meta|link)[^>]*?>.*?</\1>#si', '', $html);

        // Strip inline style attributes
        $clean = preg_replace('/\s*style\s*=\s*(["\']).*?\1/si', '', $clean);

        // Strip color, face, size, dir, class attributes that often come from Word or other CMS
        $clean = preg_replace('/\s*(face|color|size|dir|class)\s*=\s*(["\']).*?\1/si', '', $clean);

        // Remove empty spans or unwrap spans
        $clean = preg_replace('/<span[^>]*>(.*?)<\/span>/si', '$1', $clean);

        return trim($clean);
    }
}
