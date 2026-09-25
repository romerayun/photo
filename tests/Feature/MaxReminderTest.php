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
}
