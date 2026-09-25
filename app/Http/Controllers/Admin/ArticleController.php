<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleImage;
use App\Services\ImageOptimizer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(Request $request): View
    {
        $query = Article::withCount(['comments', 'images']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $articles = $query->latest('created_at')->paginate(15)->withQueryString();

        return view('admin.articles.index', [
            'articles' => $articles,
        ]);
    }

    public function create(): View
    {
        return view('admin.articles.create');
    }

    public function store(Request $request, ImageOptimizer $optimizer): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:articles,slug'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'cover' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:20480'],
            'photos.*' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:20480'],
            'reading_time' => ['nullable', 'integer', 'min:1', 'max:120'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        if (empty($validated['slug'])) {
            $baseSlug = Str::slug($validated['title']);
            if (empty($baseSlug)) {
                $baseSlug = 'article-' . time();
            }
            $count = Article::where('slug', 'like', "{$baseSlug}%")->count();
            $validated['slug'] = $count ? "{$baseSlug}-" . ($count + 1) : $baseSlug;
        }

        $validated['is_published'] = $request->boolean('is_published', true);
        $validated['published_at'] = !empty($validated['published_at']) 
            ? Carbon::parse($validated['published_at']) 
            : ($validated['is_published'] ? now() : null);

        // Upload and optimize cover
        if ($request->hasFile('cover')) {
            $optimized = $optimizer->optimizeAndStore(
                $request->file('cover'),
                'articles/covers',
                $validated['slug'],
                ImageOptimizer::MAX_SERIES_DIMENSION
            );
            $validated['cover_image'] = $optimized['path'];
        }

        $validated['content'] = $this->sanitizeContent($validated['content']);

        $article = Article::create($validated);

        // Upload additional gallery photos if provided
        if ($request->hasFile('photos')) {
            $order = 0;
            foreach ($request->file('photos') as $photoFile) {
                $order++;
                $optimizedPhoto = $optimizer->optimizeAndStore(
                    $photoFile,
                    "articles/{$article->id}",
                    $article->slug . "-img-{$order}",
                    ImageOptimizer::MAX_SERIES_DIMENSION
                );

                $article->images()->create([
                    'image_path' => $optimizedPhoto['path'],
                    'sort_order' => $order,
                ]);
            }
        }

        return redirect()->route('admin.articles.index')
            ->with('success', 'Статья успешно создана!');
    }

    public function edit(Article $article): View
    {
        $article->load(['images', 'allComments']);

        return view('admin.articles.edit', [
            'article' => $article,
        ]);
    }

    public function update(Request $request, Article $article, ImageOptimizer $optimizer): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:articles,slug,' . $article->id],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string'],
            'cover' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:20480'],
            'photos.*' => ['nullable', 'file', 'image', 'mimes:jpeg,png,jpg,webp', 'max:20480'],
            'reading_time' => ['nullable', 'integer', 'min:1', 'max:120'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
        ]);

        if (empty($validated['slug'])) {
            $baseSlug = Str::slug($validated['title']);
            if (empty($baseSlug)) {
                $baseSlug = 'article-' . time();
            }
            $count = Article::where('slug', 'like', "{$baseSlug}%")->where('id', '!=', $article->id)->count();
            $validated['slug'] = $count ? "{$baseSlug}-" . ($count + 1) : $baseSlug;
        }

        $validated['is_published'] = $request->boolean('is_published');
        $validated['published_at'] = !empty($validated['published_at']) 
            ? Carbon::parse($validated['published_at']) 
            : ($validated['is_published'] ? ($article->published_at ?? now()) : null);

        // Upload new cover if provided
        if ($request->hasFile('cover')) {
            // Delete old cover if not demo
            if ($article->cover_image && !str_starts_with($article->cover_image, 'demo/') && Storage::disk('public')->exists($article->cover_image)) {
                Storage::disk('public')->delete($article->cover_image);
            }

            $optimized = $optimizer->optimizeAndStore(
                $request->file('cover'),
                'articles/covers',
                $validated['slug'],
                ImageOptimizer::MAX_SERIES_DIMENSION
            );
            $validated['cover_image'] = $optimized['path'];
        }

        $validated['content'] = $this->sanitizeContent($validated['content']);

        $article->update($validated);

        // Upload additional gallery photos if provided
        if ($request->hasFile('photos')) {
            $maxOrder = $article->images()->max('sort_order') ?? 0;
            foreach ($request->file('photos') as $photoFile) {
                $maxOrder++;
                $optimizedPhoto = $optimizer->optimizeAndStore(
                    $photoFile,
                    "articles/{$article->id}",
                    $article->slug . "-img-{$maxOrder}",
                    ImageOptimizer::MAX_SERIES_DIMENSION
                );

                $article->images()->create([
                    'image_path' => $optimizedPhoto['path'],
                    'sort_order' => $maxOrder,
                ]);
            }
        }

        return redirect()->route('admin.articles.edit', $article)
            ->with('success', 'Изменения в статье успешно сохранены!');
    }

    public function destroy(Article $article): RedirectResponse
    {
        // Delete cover
        if ($article->cover_image && !str_starts_with($article->cover_image, 'demo/') && Storage::disk('public')->exists($article->cover_image)) {
            Storage::disk('public')->delete($article->cover_image);
        }

        // Delete gallery photos
        foreach ($article->images as $img) {
            if ($img->image_path && !str_starts_with($img->image_path, 'demo/') && Storage::disk('public')->exists($img->image_path)) {
                Storage::disk('public')->delete($img->image_path);
            }
        }

        $article->delete();

        return redirect()->route('admin.articles.index')
            ->with('success', 'Статья успешно удалена.');
    }

    public function destroyPhoto(Article $article, ArticleImage $image): RedirectResponse
    {
        if ($image->article_id !== $article->id) {
            abort(403);
        }

        if ($image->image_path && !str_starts_with($image->image_path, 'demo/') && Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return back()->with('success', 'Фотография удалена.');
    }

    /**
     * Upload an inline image from rich text editor.
     */
    public function uploadInlineImage(Request $request, ImageOptimizer $optimizer): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:20480'],
        ], [
            'image.required' => 'Изображение не передано.',
            'image.image' => 'Файл должен быть изображением.',
            'image.mimes' => 'Поддерживаются форматы JPG, PNG, WEBP и GIF.',
            'image.max' => 'Максимальный размер файла: 20 МБ.',
        ]);

        $optimized = $optimizer->optimizeAndStore(
            $request->file('image'),
            'articles/content',
            'art-img-' . time(),
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

