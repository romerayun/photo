<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\Series;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Services\ImageOptimizer;

class PhotoController extends Controller
{
    public function store(Request $request, Series $series, ImageOptimizer $optimizer): RedirectResponse
    {
        $request->validate([
            'photos' => ['required', 'array'],
            'photos.*' => ['file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:51200'],
        ], [
            'photos.*.image' => 'Файл должен быть изображением.',
            'photos.*.mimes' => 'Поддерживаются только форматы JPG, PNG и WEBP.',
            'photos.*.max' => 'Максимальный размер одного файла: 50 МБ.',
        ]);

        $currentMaxOrder = $series->photos()->max('sort_order') ?? 0;
        $semanticSlug = $series->slug ?: $series->title_ru;

        foreach ($request->file('photos') as $uploadedFile) {
            $currentMaxOrder++;

            // Optimize for SEO: progressive JPEG, max 2560px, orientation auto-correction, semantic filename
            $optimized = $optimizer->optimizeAndStore(
                $uploadedFile,
                "series/{$series->id}",
                $semanticSlug,
                ImageOptimizer::MAX_SERIES_DIMENSION
            );

            $photo = Photo::create([
                'series_id' => $series->id,
                'image_path' => $optimized['path'],
                'alt_ru' => $series->title_ru,
                'alt_en' => $series->title_en,
                'width' => $optimized['width'],
                'height' => $optimized['height'],
                'sort_order' => $currentMaxOrder,
            ]);

            // Auto set cover if none is chosen
            if (empty($series->cover_image)) {
                $series->update(['cover_image' => $optimized['path']]);
            }
        }

        return back()->with('success', 'Фотографии успешно загружены и оптимизированы для SEO.');
    }

    public function setCover(Photo $photo): RedirectResponse
    {
        $photo->series->update([
            'cover_image' => $photo->image_path,
        ]);

        return back()->with('success', 'Фотография назначена обложкой серии.');
    }

    public function destroy(Photo $photo): RedirectResponse
    {
        $series = $photo->series;
        $imagePath = $photo->image_path;

        // If cover was this photo, unset or pick next
        if ($series->cover_image === $imagePath) {
            $nextPhoto = $series->photos()->where('id', '!=', $photo->id)->first();
            $series->update([
                'cover_image' => $nextPhoto ? $nextPhoto->image_path : null,
            ]);
        }

        if (Storage::disk('public')->exists($imagePath) && !str_starts_with($imagePath, 'demo/')) {
            Storage::disk('public')->delete($imagePath);
        }

        $photo->delete();

        return back()->with('success', 'Фотография удалена.');
    }

    public function reorder(Request $request, Series $series): JsonResponse
    {
        $order = $request->input('order', []);
        foreach ($order as $index => $photoId) {
            Photo::where('id', $photoId)
                ->where('series_id', $series->id)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['status' => 'ok']);
    }
}
