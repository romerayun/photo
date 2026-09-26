<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Category;
use App\Models\SeoMeta;
use App\Models\Series;
use Illuminate\Support\Facades\Route;

class SeoService
{
    /**
     * Known static public pages.
     */
    public static function staticPages(): array
    {
        return [
            '/' => [
                'name' => 'Главная страница',
                'title' => 'Роман Юн — Фотограф в Иркутске | Портфолио и серии съёмок',
                'description' => 'Авторская фотография в Иркутске: портреты, истории для пар и семей, события и контент для брендов. Роман Юн.',
            ],
            '/portfolio' => [
                'name' => 'Портфолио (все серии)',
                'title' => 'Портфолио и серии — Роман Юн',
                'description' => 'Каждая серия — это отдельная законченная история с продуманным ритмом и настроением',
            ],
            '/articles' => [
                'name' => 'Статьи и заметки',
                'title' => 'Статьи и заметки о фотографии — Роман Юн • Иркутск',
                'description' => 'Полезные статьи о фотосессиях в Иркутске, подготовке к портретной и парной съёмке, выборе локаций, работе со светом и естественности в кадре.',
            ],
            '/pricing' => [
                'name' => 'Услуги и цены',
                'title' => 'Пакеты услуг — Роман Юн',
                'description' => 'Прозрачные форматы сотрудничества для разных творческих и коммерческих задач',
            ],
            '/about' => [
                'name' => 'О фотографе',
                'title' => 'О фотографе — Роман Юн',
                'description' => 'Снимаю в Иркутске и окрестностях. Мне важна честная атмосфера кадра — без искусственных поз и визуального шума.',
            ],
            '/contacts' => [
                'name' => 'Контакты',
                'title' => 'Контакты — Роман Юн',
                'description' => 'Свяжитесь со мной для бронирования съёмки или консультации в Иркутске',
            ],
            '/sitemap' => [
                'name' => 'Карта сайта',
                'title' => 'Карта сайта — Роман Юн • Фотограф в Иркутске',
                'description' => 'Полная структура сайта фотографа Романа Юна: основные разделы, направления съёмок, фотосерии портфолио, тарифы и контактная информация.',
            ],
        ];
    }

    /**
     * Scan and register all currently existing pages into seo_metas if not yet created.
     */
    public static function syncAllPages(): array
    {
        $createdCount = 0;
        $existingCount = 0;

        // 1. Static pages
        foreach (static::staticPages() as $path => $info) {
            $normalized = SeoMeta::normalizePath($path);
            $record = SeoMeta::where('path', $normalized)->first();
            if (!$record) {
                SeoMeta::create([
                    'path' => $normalized,
                    'title' => $info['title'],
                    'description' => $info['description'],
                    'og_title' => $info['title'],
                    'og_description' => $info['description'],
                    'canonical' => url($normalized === '/' ? '' : $normalized),
                    'robots' => 'index, follow',
                    'is_auto_generated' => true,
                ]);
                $createdCount++;
            } else {
                $existingCount++;
            }
        }

        // 2. Series pages
        $allSeries = Series::all();
        foreach ($allSeries as $series) {
            $path = SeoMeta::normalizePath('/series/' . $series->slug);
            $record = SeoMeta::where('path', $path)->first();
            if (!$record) {
                $title = ($series->title_ru ?: 'Серия съёмки') . ' — Роман Юн';
                $description = $series->description_ru ?: 'Серия фотографий Романа Юна в Иркутске';
                $ogImage = $series->cover_url ?: null;

                SeoMeta::create([
                    'path' => $path,
                    'title' => $title,
                    'description' => $description,
                    'og_title' => $title,
                    'og_description' => $description,
                    'og_image' => $ogImage,
                    'canonical' => route('series.show', ['slug' => $series->slug]),
                    'robots' => $series->is_published ? 'index, follow' : 'noindex, nofollow',
                    'is_auto_generated' => true,
                ]);
                $createdCount++;
            } else {
                $existingCount++;
            }
        }

        // 3. Article pages
        $allArticles = Article::all();
        foreach ($allArticles as $art) {
            $path = SeoMeta::normalizePath('/articles/' . $art->slug);
            $record = SeoMeta::where('path', $path)->first();
            if (!$record) {
                $title = ($art->meta_title ?: $art->title) . ' — Роман Юн';
                $desc = $art->meta_description ?: ($art->excerpt ?: strip_tags($art->content));
                $description = mb_substr(trim(preg_replace('/\s+/', ' ', $desc)), 0, 160);
                $ogImage = $art->cover_url ?: null;

                SeoMeta::create([
                    'path' => $path,
                    'title' => $title,
                    'description' => $description,
                    'og_title' => $title,
                    'og_description' => $description,
                    'og_image' => $ogImage,
                    'canonical' => route('articles.show', ['slug' => $art->slug]),
                    'robots' => $art->is_published ? 'index, follow' : 'noindex, nofollow',
                    'is_auto_generated' => true,
                ]);
                $createdCount++;
            } else {
                $existingCount++;
            }
        }

        // 4. Category pages
        $allCategories = Category::all();
        foreach ($allCategories as $cat) {
            $path = SeoMeta::normalizePath('/category/' . $cat->slug);
            $record = SeoMeta::where('path', $path)->first();
            if (!$record) {
                $title = ($cat->meta_title ?: ($cat->name_ru . ' — Фотограф Роман Юн • Иркутск'));
                $desc = $cat->meta_description ?: ($cat->description_ru ?: ('Услуги фотосъёмки в категории ' . $cat->name_ru . ' в Иркутске.'));
                $description = mb_substr(trim(preg_replace('/\s+/', ' ', $desc)), 0, 160);
                $ogImage = $cat->image_url ?: null;

                SeoMeta::create([
                    'path' => $path,
                    'title' => $title,
                    'description' => $description,
                    'og_title' => $title,
                    'og_description' => $description,
                    'og_image' => $ogImage,
                    'canonical' => route('categories.show', ['slug' => $cat->slug]),
                    'robots' => 'index, follow',
                    'is_auto_generated' => true,
                ]);
                $createdCount++;
            } else {
                $existingCount++;
            }
        }

        return [
            'created' => $createdCount,
            'existing' => $existingCount,
            'total' => $createdCount + $existingCount,
        ];
    }

    /**
     * Auto-sync or update SEO when a Series is created/updated.
     */
    public static function syncSeries(Series $series, ?string $oldSlug = null): SeoMeta
    {
        if ($oldSlug && $oldSlug !== $series->slug) {
            $oldPath = SeoMeta::normalizePath('/series/' . $oldSlug);
            SeoMeta::where('path', $oldPath)->delete();
        }

        $path = SeoMeta::normalizePath('/series/' . $series->slug);
        $record = SeoMeta::where('path', $path)->first();

        $defaultTitle = ($series->title_ru ?: 'Серия съёмки') . ' — Роман Юн';
        $defaultDesc = $series->description_ru ?: 'Серия фотографий Романа Юна в Иркутске';
        $ogImage = $series->cover_url ?: null;
        $robots = $series->is_published ? 'index, follow' : 'noindex, nofollow';

        if (!$record) {
            return SeoMeta::create([
                'path' => $path,
                'title' => $defaultTitle,
                'description' => $defaultDesc,
                'og_title' => $defaultTitle,
                'og_description' => $defaultDesc,
                'og_image' => $ogImage,
                'canonical' => route('series.show', ['slug' => $series->slug]),
                'robots' => $robots,
                'is_auto_generated' => true,
            ]);
        }

        // If it was auto-generated or not manually overridden, keep it fresh
        if ($record->is_auto_generated) {
            $record->update([
                'title' => $defaultTitle,
                'description' => $defaultDesc,
                'og_title' => $defaultTitle,
                'og_description' => $defaultDesc,
                'og_image' => $ogImage,
                'canonical' => route('series.show', ['slug' => $series->slug]),
                'robots' => $robots,
            ]);
        }

        return $record;
    }

    /**
     * Auto-sync or update SEO when an Article is created/updated.
     */
    public static function syncArticle(Article $article, ?string $oldSlug = null): SeoMeta
    {
        if ($oldSlug && $oldSlug !== $article->slug) {
            $oldPath = SeoMeta::normalizePath('/articles/' . $oldSlug);
            SeoMeta::where('path', $oldPath)->delete();
        }

        $path = SeoMeta::normalizePath('/articles/' . $article->slug);
        $record = SeoMeta::where('path', $path)->first();

        $defaultTitle = ($article->meta_title ?: $article->title) . ' — Роман Юн';
        $desc = $article->meta_description ?: ($article->excerpt ?: strip_tags($article->content));
        $defaultDesc = mb_substr(trim(preg_replace('/\s+/', ' ', $desc)), 0, 160);
        $ogImage = $article->cover_url ?: null;
        $robots = $article->is_published ? 'index, follow' : 'noindex, nofollow';

        if (!$record) {
            return SeoMeta::create([
                'path' => $path,
                'title' => $defaultTitle,
                'description' => $defaultDesc,
                'og_title' => $defaultTitle,
                'og_description' => $defaultDesc,
                'og_image' => $ogImage,
                'canonical' => route('articles.show', ['slug' => $article->slug]),
                'robots' => $robots,
                'is_auto_generated' => true,
            ]);
        }

        if ($record->is_auto_generated) {
            $record->update([
                'title' => $defaultTitle,
                'description' => $defaultDesc,
                'og_title' => $defaultTitle,
                'og_description' => $defaultDesc,
                'og_image' => $ogImage,
                'canonical' => route('articles.show', ['slug' => $article->slug]),
                'robots' => $robots,
            ]);
        }

        return $record;
    }

    /**
     * Auto-sync or update SEO when a Category is created/updated.
     */
    public static function syncCategory(Category $category, ?string $oldSlug = null): SeoMeta
    {
        if ($oldSlug && $oldSlug !== $category->slug) {
            $oldPath = SeoMeta::normalizePath('/category/' . $oldSlug);
            SeoMeta::where('path', $oldPath)->delete();
        }

        $path = SeoMeta::normalizePath('/category/' . $category->slug);
        $record = SeoMeta::where('path', $path)->first();

        $defaultTitle = ($category->meta_title ?: ($category->name_ru . ' — Фотограф Роман Юн • Иркутск'));
        $desc = $category->meta_description ?: ($category->description_ru ?: ('Услуги фотосъёмки в категории ' . $category->name_ru . ' в Иркутске.'));
        $defaultDesc = mb_substr(trim(preg_replace('/\s+/', ' ', $desc)), 0, 160);
        $ogImage = $category->image_url ?: null;

        if (!$record) {
            return SeoMeta::create([
                'path' => $path,
                'title' => $defaultTitle,
                'description' => $defaultDesc,
                'og_title' => $defaultTitle,
                'og_description' => $defaultDesc,
                'og_image' => $ogImage,
                'canonical' => route('categories.show', ['slug' => $category->slug]),
                'robots' => 'index, follow',
                'is_auto_generated' => true,
            ]);
        }

        if ($record->is_auto_generated) {
            $record->update([
                'title' => $defaultTitle,
                'description' => $defaultDesc,
                'og_title' => $defaultTitle,
                'og_description' => $defaultDesc,
                'og_image' => $ogImage,
                'canonical' => route('categories.show', ['slug' => $category->slug]),
            ]);
        }

        return $record;
    }
}
