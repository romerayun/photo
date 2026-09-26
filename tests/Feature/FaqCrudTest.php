<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_faq_admin(): void
    {
        $response = $this->get(route('admin.faqs.index'));
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_can_view_faqs_list(): void
    {
        $user = User::factory()->create();
        Faq::create([
            'question_ru' => 'Сколько длится съёмка?',
            'answer_ru' => 'От 1 до 2 часов.',
            'sort_order' => 1,
            'is_draft' => false,
        ]);

        $response = $this->actingAs($user)->get(route('admin.faqs.index'));
        $response->assertOk();
        $response->assertSee('Сколько длится съёмка?');
        $response->assertSee('От 1 до 2 часов.');
    }

    public function test_admin_can_create_faq(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.faqs.store'), [
            'question_ru' => 'Как подготовиться к фотосессии?',
            'answer_ru' => 'Мы согласуем образ и референсы перед съёмкой.',
            'sort_order' => 5,
            'is_draft' => '0',
        ]);

        $response->assertRedirect(route('admin.faqs.index'));
        $this->assertDatabaseHas('faqs', [
            'question_ru' => 'Как подготовиться к фотосессии?',
            'answer_ru' => 'Мы согласуем образ и референсы перед съёмкой.',
            'sort_order' => 5,
            'is_draft' => false,
        ]);
    }

    public function test_admin_can_update_faq(): void
    {
        $user = User::factory()->create();
        $faq = Faq::create([
            'question_ru' => 'Старый вопрос',
            'answer_ru' => 'Старый ответ',
            'sort_order' => 1,
            'is_draft' => true,
        ]);

        $response = $this->actingAs($user)->put(route('admin.faqs.update', $faq), [
            'question_ru' => 'Обновлённый вопрос',
            'answer_ru' => 'Обновлённый ответ',
            'sort_order' => 2,
            'is_draft' => '1',
        ]);

        $response->assertRedirect(route('admin.faqs.index'));
        $this->assertDatabaseHas('faqs', [
            'id' => $faq->id,
            'question_ru' => 'Обновлённый вопрос',
            'answer_ru' => 'Обновлённый ответ',
            'is_draft' => true,
        ]);
    }

    public function test_admin_can_toggle_faq_draft_status(): void
    {
        $user = User::factory()->create();
        $faq = Faq::create([
            'question_ru' => 'Тестовый вопрос',
            'answer_ru' => 'Тестовый ответ',
            'is_draft' => true,
        ]);

        $response = $this->actingAs($user)->post(route('admin.faqs.toggle', $faq));
        $response->assertRedirect();
        $this->assertFalse($faq->fresh()->is_draft);

        $response = $this->actingAs($user)->post(route('admin.faqs.toggle', $faq));
        $response->assertRedirect();
        $this->assertTrue($faq->fresh()->is_draft);
    }

    public function test_admin_can_delete_faq(): void
    {
        $user = User::factory()->create();
        $faq = Faq::create([
            'question_ru' => 'Вопрос на удаление',
            'answer_ru' => 'Ответ на удаление',
            'is_draft' => true,
        ]);

        $response = $this->actingAs($user)->delete(route('admin.faqs.destroy', $faq));
        $response->assertRedirect(route('admin.faqs.index'));
        $this->assertDatabaseMissing('faqs', [
            'id' => $faq->id,
        ]);
    }
}
