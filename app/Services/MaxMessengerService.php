<?php

namespace App\Services;

use App\Models\Shoot;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MaxMessengerService
{
    protected string $token;
    protected string $apiUrl;
    protected string $botUsername;

    public function __construct()
    {
        $this->token = config('services.max.bot_token', '');
        $this->apiUrl = rtrim(config('services.max.api_url', 'https://api.max.ru'), '/');
        $this->botUsername = config('services.max.bot_username', 'se14454241_bot');
    }

    /**
     * Get the bot username.
     */
    public function getBotUsername(): string
    {
        return $this->botUsername;
    }

    /**
     * Generate the deep link to start the bot with code payload.
     * https://max.ru/<bot_username>?start=<code>
     */
    public function getStartLink(string $plainCode): string
    {
        return "https://max.ru/{$this->botUsername}?start={$plainCode}";
    }

    /**
     * Send a text message to a user or chat in MAX.
     */
    public function sendMessage(string $chatIdOrUserId, string $text): bool
    {
        if (empty($this->token)) {
            Log::warning('MaxMessengerService: bot_token is not configured.');
            return false;
        }

        try {
            // Attempt standard bot sendMessage endpoints
            $endpoints = [
                "{$this->apiUrl}/bot/v1/messages/sendText",
                "{$this->apiUrl}/messages/sendText",
                "{$this->apiUrl}/sendMessage",
            ];

            $payload = [
                'chat_id' => $chatIdOrUserId,
                'user_id' => $chatIdOrUserId,
                'text' => $text,
            ];

            foreach ($endpoints as $endpoint) {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$this->token}",
                    'X-Bot-Token' => $this->token,
                ])->timeout(5)->post($endpoint . "?token=" . urlencode($this->token), $payload);

                if ($response->successful()) {
                    Log::info("MAX message sent successfully to {$chatIdOrUserId}");
                    return true;
                }
            }

            Log::warning("MAX message failed across endpoints to {$chatIdOrUserId}");
            return false;
        } catch (\Throwable $e) {
            Log::error("MAX sendMessage exception: " . $e->getMessage(), [
                'recipient' => $chatIdOrUserId,
            ]);
            return false;
        }
    }

    /**
     * Send confirmation message when client links their shoot.
     */
    public function sendConfirmation(Shoot $shoot): bool
    {
        $target = $shoot->max_chat_id ?: $shoot->max_user_id;
        if (!$target) {
            return false;
        }

        $dateFormatted = $shoot->shoot_date ? $shoot->shoot_date->format('d.m.Y') : '';
        $time = substr($shoot->start_time, 0, 5);

        $text = "Здравствуйте, {$shoot->client_name}! 👋\n\n"
            . "Напоминания о вашей фотосессии успешно подключены в MAX.\n\n"
            . "📅 Дата: {$dateFormatted}\n"
            . "⏰ Время: {$time}\n"
            . ($shoot->location ? "📍 Локация: {$shoot->location}\n\n" : "\n")
            . "Мы пришлем вам уведомление перед съемкой, чтобы вы ничего не забыли!";

        return $this->sendMessage($target, $text);
    }
}
