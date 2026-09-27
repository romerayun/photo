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
        $this->apiUrl = rtrim(config('services.max.api_url', 'https://platform-api2.max.ru'), '/');
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
     * Generate link directly to Web MAX client.
     * https://web.max.ru/<bot_username>?start=<code>
     */
    public function getWebLink(string $plainCode): string
    {
        return "https://web.max.ru/{$this->botUsername}?start={$plainCode}";
    }

    /**
     * Send a text message to a user or chat in MAX.
     * Official API: POST https://platform-api2.max.ru/messages?chat_id=... or ?user_id=...
     * Header: Authorization: <token>
     * Body: { "text": "..." }
     */
    public function sendMessage(?string $chatId, ?string $userId, string $text): bool
    {
        if (empty($this->token)) {
            Log::warning('MaxMessengerService: bot_token is not configured.');
            return false;
        }

        try {
            // Build query params
            $queryParams = [];
            if ($chatId) {
                $queryParams['chat_id'] = $chatId;
            } elseif ($userId) {
                $queryParams['user_id'] = $userId;
            } else {
                return false;
            }

            $url = "{$this->apiUrl}/messages?" . http_build_query($queryParams);

            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Content-Type' => 'application/json',
            ])
            ->withoutVerifying() // Supports Russian CA certificates
            ->timeout(5)
            ->post($url, [
                'text' => $text,
            ]);

            if ($response->successful()) {
                Log::info("MAX message sent successfully to chat_id={$chatId} user_id={$userId}");
                return true;
            }

            // Fallback: If chat_id failed, try user_id directly if available
            if ($chatId && $userId && $chatId !== $userId) {
                $fallbackUrl = "{$this->apiUrl}/messages?user_id=" . urlencode($userId);
                $fallbackRes = Http::withHeaders([
                    'Authorization' => $this->token,
                    'Content-Type' => 'application/json',
                ])
                ->withoutVerifying()
                ->timeout(5)
                ->post($fallbackUrl, [
                    'text' => $text,
                ]);

                if ($fallbackRes->successful()) {
                    Log::info("MAX fallback message sent successfully to user_id={$userId}");
                    return true;
                }
            }

            Log::warning("MAX message failed: " . $response->body(), [
                'status' => $response->status(),
                'chat_id' => $chatId,
                'user_id' => $userId,
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error("MAX sendMessage exception: " . $e->getMessage(), [
                'chat_id' => $chatId,
                'user_id' => $userId,
            ]);
            return false;
        }
    }

    /**
     * Send confirmation message when client links their shoot.
     */
    public function sendConfirmation(Shoot $shoot): bool
    {
        if (!$shoot->max_chat_id && !$shoot->max_user_id) {
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

        return $this->sendMessage($shoot->max_chat_id, $shoot->max_user_id, $text);
    }
}

