<?php

namespace Tests\Feature;

use App\Models\Shoot;
use App\Services\MaxMessengerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MaxReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_can_generate_max_deep_link_with_one_time_token(): void
    {
        $shoot = Shoot::create([
            'client_name' => 'Анна Смирнова',
            'shoot_date' => now()->addDays(2)->format('Y-m-d'),
            'start_time' => '14:00',
            'duration_minutes' => 60,
            'status' => 'planned',
            'share_token' => 'test-share-token-123',
        ]);

        $response = $this->postJson(route('shoots.share.max_link', ['token' => $shoot->share_token]));

        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'deep_link', 'expires_in_minutes', 'bot_username']);
        
        $shoot->refresh();
        $this->assertNotNull($shoot->max_link_code_hash);
        $this->assertNotNull($shoot->max_link_code_expires_at);
        $this->assertTrue($shoot->max_link_code_expires_at->isFuture());

        // Verify deep link structure: https://max.ru/<bot_username>?start=<code>
        $data = $response->json();
        $this->assertStringStartsWith('https://max.ru/se14454241_bot?start=', $data['deep_link']);
    }

    public function test_max_webhook_atomically_links_user_and_redeems_code(): void
    {
        Http::fake([
            '*' => Http::response(['ok' => true], 200),
        ]);

        $plainCode = 'secret-test-code-999';
        $codeHash = hash('sha256', $plainCode);

        $shoot = Shoot::create([
            'client_name' => 'Михаил Кузнецов',
            'shoot_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '16:00',
            'duration_minutes' => 120,
            'status' => 'planned',
            'share_token' => 'share-code-xyz',
            'max_link_code_hash' => $codeHash,
            'max_link_code_expires_at' => now()->addMinutes(15),
        ]);

        $webhookPayload = [
            'event' => 'bot_started',
            'payload' => $plainCode,
            'user' => [
                'user_id' => 'max_user_8089555',
            ],
            'chat_id' => 'max_chat_8089555',
        ];

        $response = $this->postJson(route('webhook.max'), $webhookPayload);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ok',
            'shoot_id' => $shoot->id,
        ]);

        $shoot->refresh();
        // Check atomic redemption: hash and expiration must be cleared
        $this->assertNull($shoot->max_link_code_hash);
        $this->assertNull($shoot->max_link_code_expires_at);
        $this->assertEquals('max_user_8089555', $shoot->max_user_id);
        $this->assertEquals('max_chat_8089555', $shoot->max_chat_id);
        $this->assertNotNull($shoot->max_connected_at);

        // Submitting same code again must fail (already redeemed)
        $secondResponse = $this->postJson(route('webhook.max'), $webhookPayload);
        $secondResponse->assertStatus(400);
    }

    public function test_client_with_multiple_shoots_receives_selection_menu_in_bot(): void
    {
        $sentRequests = [];
        Http::fake(function ($request) use (&$sentRequests) {
            $sentRequests[] = $request->data();
            return Http::response(['ok' => true], 200);
        });

        $client = \App\Models\Client::create([
            'name' => 'Елена Попова',
            'phone' => '+7 999 111-22-33',
            'max_user_id' => 'user_multiple_123',
            'max_chat_id' => 'chat_multiple_123',
            'max_connected_at' => now(),
        ]);

        $shoot1 = Shoot::create([
            'client_id' => $client->id,
            'client_name' => $client->name,
            'shoot_date' => now()->addDays(5)->format('Y-m-d'),
            'start_time' => '10:00',
            'duration_minutes' => 60,
            'status' => 'planned',
            'location' => 'Студия Фотолофт',
            'max_user_id' => 'user_multiple_123',
            'max_chat_id' => 'chat_multiple_123',
            'share_token' => 'token-multiple-1',
        ]);

        $shoot2 = Shoot::create([
            'client_id' => $client->id,
            'client_name' => $client->name,
            'shoot_date' => now()->addDays(20)->format('Y-m-d'),
            'start_time' => '17:00',
            'duration_minutes' => 90,
            'status' => 'planned',
            'location' => 'Набережная',
            'max_user_id' => 'user_multiple_123',
            'max_chat_id' => 'chat_multiple_123',
            'share_token' => 'token-multiple-2',
        ]);

        // When client clicks "shoot_details" callback
        $response = $this->postJson(route('webhook.max'), [
            'event' => 'message_callback',
            'callback' => [
                'callback_id' => 'cb_multiple_test',
                'payload' => 'shoot_details',
                'user' => ['user_id' => 'user_multiple_123'],
            ],
            'chat_id' => 'chat_multiple_123',
        ]);

        $response->assertStatus(200);

        // Verify sent message contains prompt for multiple shoots and options
        $lastRequest = end($sentRequests);
        $this->assertNotEmpty($lastRequest);
        $this->assertStringContainsString('несколько съёмок', $lastRequest['text'] ?? '');

        // Verify callback shoot_select_{id} provides specific details
        $selectResponse = $this->postJson(route('webhook.max'), [
            'event' => 'message_callback',
            'callback' => [
                'callback_id' => 'cb_select_1',
                'payload' => "shoot_select_{$shoot1->id}",
                'user' => ['user_id' => 'user_multiple_123'],
            ],
            'chat_id' => 'chat_multiple_123',
        ]);

        $selectResponse->assertStatus(200);
        $detailRequest = end($sentRequests);
        $this->assertStringContainsString('Студия Фотолофт', $detailRequest['text'] ?? '');

        // Verify "shoot_card_menu" callback also asks user to choose which card to open
        $cardResponse = $this->postJson(route('webhook.max'), [
            'event' => 'message_callback',
            'callback' => [
                'callback_id' => 'cb_card_multiple',
                'payload' => 'shoot_card_menu',
                'user' => ['user_id' => 'user_multiple_123'],
            ],
            'chat_id' => 'chat_multiple_123',
        ]);

        $cardResponse->assertStatus(200);
        $cardRequest = end($sentRequests);
        $this->assertStringContainsString('Выберите карточку съёмки', $cardRequest['text'] ?? '');
    }

    public function test_user_without_shoots_receives_no_shoots_message_and_booking_buttons(): void
    {
        $sentRequests = [];
        Http::fake(function ($request) use (&$sentRequests) {
            $sentRequests[] = $request->data();
            return Http::response(['ok' => true], 200);
        });

        // User without any shoots sends a text message to bot
        $response = $this->postJson(route('webhook.max'), [
            'update_type' => 'message_created',
            'chat_id' => 'chat_no_shoots_999',
            'user' => ['user_id' => 'user_no_shoots_999'],
            'message' => [
                'text' => 'Привет',
            ],
        ]);

        $response->assertStatus(200);
        $lastRequest = end($sentRequests);
        $this->assertNotEmpty($lastRequest);
        $this->assertStringContainsString('нет запланированных съёмок', $lastRequest['text'] ?? '');

        // Verify attachments only have 2 buttons: "Записаться на съёмку" and "Контакты фотографа"
        $buttons = $lastRequest['attachments'][0]['payload']['buttons'] ?? [];
        $this->assertCount(2, $buttons);
        $this->assertEquals('📅 Записаться на съёмку', $buttons[0][0]['text']);
        $this->assertEquals('link', $buttons[0][0]['type']);
        $this->assertEquals('📞 Контакты фотографа', $buttons[1][0]['text']);
        $this->assertEquals('callback', $buttons[1][0]['type']);
    }
}

