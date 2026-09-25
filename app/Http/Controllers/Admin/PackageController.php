<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PackageController extends Controller
{
    public function index(): View
    {
        $packages = Package::orderBy('sort_order')->get();
        return view('admin.packages.index', ['packages' => $packages]);
    }

    public function create(): View
    {
        return view('admin.packages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title_ru' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:packages,slug'],
            'subtitle_ru' => ['nullable', 'string', 'max:255'],
            'subtitle_en' => ['nullable', 'string', 'max:255'],
            'duration_ru' => ['nullable', 'string', 'max:255'],
            'duration_en' => ['nullable', 'string', 'max:255'],
            'photo_count_ru' => ['nullable', 'string', 'max:255'],
            'photo_count_en' => ['nullable', 'string', 'max:255'],
            'includes_ru' => ['nullable', 'string'],
            'includes_en' => ['nullable', 'string'],
            'delivery_time_ru' => ['nullable', 'string', 'max:255'],
            'delivery_time_en' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'integer', 'min:0'],
            'is_price_from' => ['boolean'],
            'extra_conditions_ru' => ['nullable', 'string'],
            'extra_conditions_en' => ['nullable', 'string'],
            'is_published' => ['boolean'],
            'sort_order' => ['integer'],
        ]);

        if (empty($validated['slug'])) {
            $slugBase = !empty($validated['title_en']) ? $validated['title_en'] : $validated['title_ru'];
            $slug = Str::slug($slugBase);
            if (empty($slug)) {
                $slug = 'package-' . time();
            }
            $count = Package::where('slug', 'like', "{$slug}%")->count();
            $validated['slug'] = $count ? "{$slug}-" . ($count + 1) : $slug;
        }

        $validated['is_price_from'] = $request->boolean('is_price_from');
        $validated['is_published'] = $request->boolean('is_published');
        $validated['sort_order'] = $request->input('sort_order', 0);

        $package = Package::create($validated);

        return redirect()->route('admin.packages.index')->with('success', "Пакет '{$package->title_ru}' успешно создан.");
    }

    public function edit(Package $package): View
    {
        return view('admin.packages.edit', ['package' => $package]);
    }

    public function update(Request $request, Package $package): RedirectResponse
    {
        $validated = $request->validate([
            'title_ru' => ['required', 'string', 'max:255'],
            'title_en' => ['nullable', 'string', 'max:255'],
            'subtitle_ru' => ['nullable', 'string', 'max:255'],
            'subtitle_en' => ['nullable', 'string', 'max:255'],
            'duration_ru' => ['nullable', 'string', 'max:255'],
            'duration_en' => ['nullable', 'string', 'max:255'],
            'photo_count_ru' => ['nullable', 'string', 'max:255'],
            'photo_count_en' => ['nullable', 'string', 'max:255'],
            'includes_ru' => ['nullable', 'string'],
            'includes_en' => ['nullable', 'string'],
            'delivery_time_ru' => ['nullable', 'string', 'max:255'],
            'delivery_time_en' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'integer', 'min:0'],
            'is_price_from' => ['boolean'],
            'extra_conditions_ru' => ['nullable', 'string'],
            'extra_conditions_en' => ['nullable', 'string'],
            'is_published' => ['boolean'],
            'sort_order' => ['integer'],
        ]);

        $validated['is_price_from'] = $request->boolean('is_price_from');
        $validated['is_published'] = $request->boolean('is_published');
        $validated['sort_order'] = $request->input('sort_order', 0);

        $package->update($validated);

        return redirect()->route('admin.packages.index')->with('success', "Пакет '{$package->title_ru}' успешно обновлён.");
    }

    public function destroy(Package $package): RedirectResponse
    {
        $title = $package->title_ru;
        $package->delete();

        return redirect()->route('admin.packages.index')->with('success', "Пакет '{$title}' успешно удалён.");
    }
}
