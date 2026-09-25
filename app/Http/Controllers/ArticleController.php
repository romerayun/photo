<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    /**
     * Display a listing of articles (12 per page).
     */
    public function index(Request $request): View
    {
        $articles = Article::published()
            ->withCount('comments')
            ->latest('published_at')
            ->paginate(12)
            ->withQueryString();

        return view('pages.articles.index', [
            'articles' => $articles,
        ]);
    }

    /**
     * Display a specific article.
     */
    public function show(Request $request, string $slug): View
    {
        $article = Article::published()
            ->where('slug', $slug)
            ->with(['comments', 'images'])
            ->firstOrFail();

        // Increment views count once per session
        $sessionKey = 'viewed_article_' . $article->id;
        if (!session()->has($sessionKey)) {
            $article->increment('views_count');
            session()->put($sessionKey, true);
        }

        // Related / recent articles
        $relatedArticles = Article::published()
            ->where('id', '!=', $article->id)
            ->withCount('comments')
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('pages.articles.show', [
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }

    /**
     * Store a comment for the article.
     */
    public function storeComment(Request $request, Article $article): RedirectResponse
    {
        // Anti-spam honeypot
        if ($request->filled('website_url')) {
            return redirect()->to(route('articles.show', $article->slug) . '#comments')
                ->with('comment_success', 'Спасибо! Ваш комментарий успешно добавлен.');
        }

        $validated = $request->validate([
            'author_name' => ['required', 'string', 'min:2', 'max:80'],
            'author_email' => ['nullable', 'email', 'max:100'],
            'content' => ['required', 'string', 'min:3', 'max:2000'],
        ], [
            'author_name.required' => 'Пожалуйста, укажите ваше имя.',
            'author_name.min' => 'Имя должно содержать не менее 2 символов.',
            'author_email.email' => 'Пожалуйста, введите корректный адрес электронной почты.',
            'content.required' => 'Пожалуйста, напишите текст комментария.',
            'content.min' => 'Текст комментария должен содержать не менее 3 символов.',
            'content.max' => 'Комментарий слишком длинный (максимум 2000 символов).',
        ]);

        $article->comments()->create([
            'author_name' => strip_tags(trim($validated['author_name'])),
            'author_email' => !empty($validated['author_email']) ? trim($validated['author_email']) : null,
            'content' => strip_tags(trim($validated['content'])),
            'is_approved' => true,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->to(route('articles.show', $article->slug) . '#comments')
            ->with('comment_success', 'Спасибо! Ваш комментарий успешно опубликован.');
    }
}
