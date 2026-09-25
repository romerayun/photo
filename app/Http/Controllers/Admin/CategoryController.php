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

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('series')->orderBy('sort_order')->get();
        return view('admin.categories.index', ['categories' => $categories]);
    }

    public function store(Request $request, ImageOptimizer $optimizer): RedirectResponse
    {
        $validated = $request->validate([
            'name_ru' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
            'description_ru' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:51200'],
            'sort_order' => ['nullable', 'integer'],
        ], [
            'image.max' => 'Максимальный размер изображения: 50 МБ.',
        ]);

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name_ru']);
        }

        $validated['sort_order'] = (int) $request->input('sort_order', 0);

        if ($request->hasFile('image')) {
            $optimized = $optimizer->optimizeAndStore(
                $request->file('image'),
                'categories',
                $validated['slug'] ?: $validated['name_ru'],
                ImageOptimizer::MAX_CATEGORY_DIMENSION
            );
            $validated['image'] = $optimized['path'];
        }

        Category::create($validated);

        return back()->with('success', 'Категория успешно добавлена.');
    }

    public function update(Request $request, Category $category, ImageOptimizer $optimizer): RedirectResponse
    {
        $validated = $request->validate([
            'name_ru' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug,' . $category->id],
            'description_ru' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:51200'],
            'sort_order' => ['nullable', 'integer'],
        ], [
            'image.max' => 'Максимальный размер изображения: 50 МБ.',
        ]);

        $validated['sort_order'] = (int) $request->input('sort_order', 0);

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

        return back()->with('success', 'Категория успешно обновлена.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        if ($category->image && Storage::disk('public')->exists($category->image)) {
            Storage::disk('public')->delete($category->image);
        }
        $category->delete();
        return back()->with('success', 'Категория удалена.');
    }
}
