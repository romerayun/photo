<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArticlesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::firstOrCreate([
            'email' => 'admin@romanyun.ru',
        ], [
            'name' => 'Роман Юн',
            'password' => Hash::make('password123'),
        ]);
    }

    public function test_articles_index_page_is_accessible_and_displays_articles(): void
    {
        $response = $this->get(route('articles.index'));

        $response->assertStatus(200);
        $response->assertSee('СТАТЬИ');
    }

    public function test_articles_page_paginates_by_12(): void
    {
        $response = $this->get(route('articles.index'));

        $response->assertStatus(200);
        // We have 14 seeded articles, so page 2 must exist
        $response->assertSee('page=2');

        $responsePage2 = $this->get(route('articles.index', ['page' => 2]));
        $responsePage2->assertStatus(200);
    }

    public function test_single_article_page_displays_content_sharing_and_views(): void
    {
        $article = Article::published()->first();
        $this->assertNotNull($article);

        $initialViews = $article->views_count;

        $response = $this->get(route('articles.show', $article->slug));
        $response->assertStatus(200);
        $response->assertSee($article->title);
        $response->assertSee('ПОДЕЛИТЬСЯ СТАТЬЕЙ');
        $response->assertSee('Telegram');
        $response->assertSee('ВКонтакте');
        $response->assertSee('Скопировать ссылку');
        $response->assertSee('Комментарии');

        // Check views incremented
        $article->refresh();
        $this->assertEquals($initialViews + 1, $article->views_count);
    }

    public function test_user_can_submit_comment_to_article(): void
    {
        $article = Article::published()->first();

        $response = $this->post(route('articles.comments.store', $article), [
            'author_name' => 'Иван Тестовый',
            'author_email' => 'ivan@example.com',
            'content' => 'Отличная познавательная статья о подготовке к фотосессии!',
        ]);

        $response->assertRedirect(route('articles.show', $article->slug) . '#comments');
        $response->assertSessionHas('comment_success');

        $this->assertDatabaseHas('comments', [
            'article_id' => $article->id,
            'author_name' => 'Иван Тестовый',
            'content' => 'Отличная познавательная статья о подготовке к фотосессии!',
            'is_approved' => true,
        ]);
    }

    public function test_comment_honeypot_drops_spam_bots(): void
    {
        $article = Article::published()->first();

        $initialCount = Comment::where('article_id', $article->id)->count();

        $response = $this->post(route('articles.comments.store', $article), [
            'author_name' => 'Spam Bot',
            'content' => 'Buy cheap links now',
            'website_url' => 'http://spam-link.com',
        ]);

        $response->assertRedirect(route('articles.show', $article->slug) . '#comments');

        $this->assertEquals($initialCount, Comment::where('article_id', $article->id)->count());
    }

    public function test_admin_can_access_articles_management(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.articles.index'));
        $response->assertStatus(200);
        $response->assertSee('Статьи и материалы');

        $createResponse = $this->actingAs($this->admin)->get(route('admin.articles.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Создать новую статью');
        $createResponse->assertSee('editor-container');
    }

    public function test_admin_can_upload_inline_image_for_rich_text_editor(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('test-photo.jpg', 1200, 800);

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.articles.upload_image'), [
                'image' => $file,
            ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['url']);

        $url = $response->json('url');
        $this->assertNotEmpty($url);
        $this->assertStringContainsString('articles/content', $url);
    }

    public function test_admin_can_create_article_with_rich_text_and_it_renders_html(): void
    {
        $htmlContent = '<h2>Совет по композиции</h2><p>Используйте естественные линии горизонта.</p><blockquote>Красота в деталях</blockquote>';

        $response = $this->actingAs($this->admin)
            ->post(route('admin.articles.store'), [
                'title' => 'Статья с форматированием текста',
                'slug' => 'statya-s-formatirovaniem',
                'excerpt' => 'Краткое содержание статьи',
                'content' => $htmlContent,
                'reading_time' => 4,
                'is_published' => 1,
                'published_at' => now()->toDateTimeString(),
            ]);

        $response->assertRedirect(route('admin.articles.index'));

        $this->assertDatabaseHas('articles', [
            'slug' => 'statya-s-formatirovaniem',
            'content' => $htmlContent,
        ]);

        $publicResponse = $this->get(route('articles.show', 'statya-s-formatirovaniem'));
        $publicResponse->assertStatus(200);
        // HTML must be rendered unescaped, not as &lt;h2&gt;
        $publicResponse->assertSee('<h2>Совет по композиции</h2>', false);
        $publicResponse->assertSee('<blockquote>Красота в деталях</blockquote>', false);
    }
}

