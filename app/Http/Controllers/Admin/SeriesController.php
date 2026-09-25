<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Series;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SeriesController extends Controller
{
    public function index(): View
    {
        $series = Series::with(['category', 'photos'])
            ->orderBy('sort_order')
            ->paginate(15);

        return view('admin.series.index', ['series' => $series]);
    }

    public function create(): View
    {
        $categories = Category::orderBy('sort_order')->get();
        return view('admin.series.create', ['categories' => $categories]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title_ru' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:series,slug'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'description_ru' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'location_ru' => ['nullable', 'string', 'max:255'],
            'location_en' => ['nullable', 'string', 'max:255'],
            'shooting_date' => ['nullable', 'string', 'max:100'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'is_demo' => ['boolean'],
            'sort_order' => ['integer'],
        ]);

        if (empty($validated['slug'])) {
            $slugBase = !empty($validated['title_en']) ? $validated['title_en'] : $validated['title_ru'];
            $slug = Str::slug($slugBase);
            if (empty($slug)) {
                $slug = 'series-' . time();
            }
            $count = Series::where('slug', 'like', "{$slug}%")->count();
            $validated['slug'] = $count ? "{$slug}-" . ($count + 1) : $slug;
        }

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_published'] = $request->boolean('is_published');
        $validated['is_demo'] = $request->boolean('is_demo');
        $validated['sort_order'] = $request->input('sort_order', 0);

        $series = Series::create($validated);

        return redirect()->route('admin.series.edit', $series)->with('success', 'Серия успешно создана. Теперь можно добавить фотографии.');
    }

    public function edit(Series $series): View
    {
        $series->load(['category', 'photos']);
        $categories = Category::orderBy('sort_order')->get();

        $seoMeta = \App\Models\SeoMeta::findByPath('/series/' . $series->slug);

        return view('admin.series.edit', [
            'series' => $series,
            'categories' => $categories,
            'seoMeta' => $seoMeta,
        ]);
    }

    public function update(Request $request, Series $series): RedirectResponse
    {
        $validated = $request->validate([
            'title_ru' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:series,slug,' . $series->id],
            'category_id' => ['nullable', 'exists:categories,id'],
            'description_ru' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'location_ru' => ['nullable', 'string', 'max:255'],
            'location_en' => ['nullable', 'string', 'max:255'],
            'shooting_date' => ['nullable', 'string', 'max:100'],
            'cover_image' => ['nullable', 'string'],
            'is_featured' => ['boolean'],
            'is_published' => ['boolean'],
            'is_demo' => ['boolean'],
            'sort_order' => ['integer'],
        ]);

        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_published'] = $request->boolean('is_published');
        $validated['is_demo'] = $request->boolean('is_demo');
        $validated['sort_order'] = $request->input('sort_order', 0);

        $series->update($validated);

        return redirect()->route('admin.series.edit', $series)->with('success', 'Изменения в серии успешно сохранены.');
    }

    public function destroy(Series $series): RedirectResponse
    {
        $series->delete();
        return redirect()->route('admin.series.index')->with('success', 'Серия удалена.');
    }
}
