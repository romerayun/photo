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
     * Send a text message to a user or chat in MAX with optional buttons.
     * Official API: POST https://platform-api2.max.ru/messages?chat_id=... or ?user_id=...
     * Header: Authorization: <token>
     * Body: { "text": "...", "attachments": [...] }
     */
    public function sendMessage(?string $chatId, ?string $userId, string $text, array $buttons = []): bool
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

            $payload = [
                'text' => $text,
                'format' => 'markdown',
            ];
            if (!empty($buttons)) {
                $payload['attachments'] = [
                    [
                        'type' => 'inline_keyboard',
                        'payload' => [
                            'buttons' => $buttons,
                        ],
                    ],
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Content-Type' => 'application/json',
            ])
            ->withoutVerifying() // Supports Russian CA certificates
            ->timeout(5)
            ->post($url, $payload);

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
                ->post($fallbackUrl, $payload);

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
     * Build active buttons based on admin settings or specific allowed button keys.
     * @param Shoot|null $shoot
     * @param array|null $allowedButtons If specified, only include these keys (e.g. ['card', 'details', 'tips', 'contacts', 'custom_0'])
     */
    public function getMenuButtons(?Shoot $shoot = null, ?array $allowedButtons = null, int $shootsCount = 1): array
    {
        $buttons = [];
        $filter = $allowedButtons !== null;

        // If client has NO shoots at all, only offer booking and contacts
        if ($shootsCount === 0 && $shoot === null) {
            $bookingUrl = \App\Models\Setting::get('max_bot_booking_url') ?: url('/contacts');
            $bookingText = \App\Models\Setting::get('max_bot_btn_book_text', '📅 Записаться на съёмку');
            $contactsText = \App\Models\Setting::get('max_bot_btn_contacts_text', '📞 Контакты фотографа');

            $buttons[] = [
                [
                    'type' => 'link',
                    'text' => $bookingText,
                    'url' => $bookingUrl,
                ],
            ];
            $buttons[] = [
                [
                    'type' => 'callback',
                    'text' => $contactsText,
                    'payload' => 'photographer_contacts',
                ],
            ];

            return $buttons;
        }

        // 1. Button to web card
        $cardEnabled = \App\Models\Setting::get('max_bot_btn_card_enabled', '1') === '1';
        $cardText = \App\Models\Setting::get('max_bot_btn_card_text', '📱 Открыть карточку съёмки');
        if ($filter ? in_array('card', $allowedButtons, true) : $cardEnabled) {
            if ($shootsCount > 1) {
                // If client has multiple shoots, show selection prompt
                $buttons[] = [
                    [
                        'type' => 'callback',
                        'text' => $cardText,
                        'payload' => 'shoot_card_menu',
                    ],
                ];
            } elseif ($shoot && $shoot->share_token) {
                // Exactly 1 shoot: direct link to web card
                $buttons[] = [
                    [
                        'type' => 'link',
                        'text' => $cardText,
                        'url' => url("/shoot/{$shoot->share_token}"),
                    ],
                ];
            }
        }

        // 2. Action buttons row (Details & Tips)
        $actionRow = [];
        $detailsEnabled = \App\Models\Setting::get('max_bot_btn_details_enabled', '1') === '1';
        $detailsText = \App\Models\Setting::get('max_bot_btn_details_text', 'ℹ️ Детали съёмки');
        if ($filter ? in_array('details', $allowedButtons, true) : $detailsEnabled) {
            $actionRow[] = ['type' => 'callback', 'text' => $detailsText, 'payload' => 'shoot_details'];
        }

        $tipsEnabled = \App\Models\Setting::get('max_bot_btn_tips_enabled', '1') === '1';
        $tipsText = \App\Models\Setting::get('max_bot_btn_tips_text', '👗 Подготовка');
        if ($filter ? in_array('tips', $allowedButtons, true) : $tipsEnabled) {
            $actionRow[] = ['type' => 'callback', 'text' => $tipsText, 'payload' => 'shoot_tips'];
        }
        if (!empty($actionRow)) {
            $buttons[] = $actionRow;
        }

        // 3. Contacts button
        $contactsEnabled = \App\Models\Setting::get('max_bot_btn_contacts_enabled', '1') === '1';
        $contactsText = \App\Models\Setting::get('max_bot_btn_contacts_text', '📞 Контакты фотографа');
        if ($filter ? in_array('contacts', $allowedButtons, true) : $contactsEnabled) {
            $buttons[] = [
                ['type' => 'callback', 'text' => $contactsText, 'payload' => 'photographer_contacts'],
            ];
        }

        // 4. Custom user buttons from admin
        $customButtons = json_decode(\App\Models\Setting::get('max_bot_custom_buttons', '[]'), true) ?: [];
        foreach ($customButtons as $index => $cBtn) {
            $title = $cBtn['title'] ?? '';
            $type = $cBtn['type'] ?? 'link';
            $inMenu = !isset($cBtn['in_menu']) || (bool)$cBtn['in_menu'];
            if (empty($title)) {
                continue;
            }

            if ($filter) {
                if (!in_array("custom_{$index}", $allowedButtons, true)) {
                    continue;
                }
            } else {
                if (!$inMenu) {
                    continue;
                }
            }

            if ($type === 'link' && !empty($cBtn['url'])) {
                $buttons[] = [
                    [
                        'type' => 'link',
                        'text' => $title,
                        'url' => $cBtn['url'],
                    ],
                ];
            } elseif ($type === 'text') {
                $buttons[] = [
                    [
                        'type' => 'callback',
                        'text' => $title,
                        'payload' => "custom_btn_{$index}",
                    ],
                ];
            }
        }

        return $buttons;
    }

    /**
     * Send confirmation messages when client links their shoot:
     * 1. Welcome message (without buttons).
     * 2. Follow-up action message (with buttons).
     */
    public function sendConfirmation(Shoot $shoot): bool
    {
        if (!$shoot->max_chat_id && !$shoot->max_user_id) {
            return false;
        }

        $dateFormatted = $shoot->shoot_date ? $shoot->shoot_date->format('d.m.Y') : '';
        $time = substr($shoot->start_time, 0, 5);

        // 1. First Welcome Message (No buttons)
        $firstTemplate = \App\Models\Setting::get('max_bot_welcome_text', 
            "Здравствуйте, {client_name}! 👋\n\n"
            . "Напоминания о вашей фотосессии успешно подключены в MAX.\n\n"
            . "📅 Дата: {date}\n"
            . "⏰ Время: {time}\n"
            . "{location}\n"
            . "Мы пришлем вам уведомление перед съёмкой, чтобы всё прошло идеально!"
        );

        $locationText = $shoot->location ? "📍 Локация: {$shoot->location}\n" : "";

        $firstText = str_replace(
            ['{client_name}', '{date}', '{time}', '{location}'],
            [$shoot->client_name, $dateFormatted, $time, $locationText],
            $firstTemplate
        );

        // Send first message without buttons
        $this->sendMessage($shoot->max_chat_id, $shoot->max_user_id, $firstText, []);

        // Small delay to ensure sequential arrival in client's messenger
        usleep(300000); // 300ms

        // 2. Second Message (With interactive menu buttons)
        $secondTemplate = \App\Models\Setting::get('max_bot_welcome_second_text', 
            "Чтобы вам было удобно, вы можете прямо сейчас посмотреть детали съёмки, памятку по подготовке или перейти в карточку съёмки кнопками ниже:"
        );

        $secondText = str_replace(
            ['{client_name}', '{date}', '{time}', '{location}'],
            [$shoot->client_name, $dateFormatted, $time, $locationText],
            $secondTemplate
        );

        $shootsCount = 1;
        $irkutskToday = now('Asia/Irkutsk')->toDateString();
        if ($shoot->client_id) {
            $shootsCount = Shoot::where('client_id', $shoot->client_id)
                ->where('shoot_date', '>=', $irkutskToday)
                ->where('status', '!=', 'cancelled')
                ->get()
                ->reject(fn($s) => $s->is_past)
                ->count();
        } elseif ($shoot->max_chat_id || $shoot->max_user_id) {
            $shootsCount = Shoot::where(function ($q) use ($shoot) {
                if ($shoot->max_chat_id) $q->where('max_chat_id', $shoot->max_chat_id);
                if ($shoot->max_user_id) $q->orWhere('max_user_id', $shoot->max_user_id);
            })
            ->where('shoot_date', '>=', $irkutskToday)
            ->where('status', '!=', 'cancelled')
            ->get()
            ->reject(fn($s) => $s->is_past)
            ->count();
        }
        $shootsCount = max(1, $shootsCount);

        $buttons = $this->getMenuButtons($shoot, null, $shootsCount);

        return $this->sendMessage($shoot->max_chat_id, $shoot->max_user_id, $secondText, $buttons);
    }

    /**
     * Answer a callback query from button click.
     * API: POST https://platform-api2.max.ru/answers?callback_id=...
     */
    public function answerCallback(string $callbackId, ?string $notificationText = null): bool
    {
        if (empty($this->token) || empty($callbackId)) {
            return false;
        }

        try {
            $url = "{$this->apiUrl}/answers?callback_id=" . urlencode($callbackId);
            $payload = [];
            if ($notificationText) {
                $payload['notification'] = $notificationText;
            }

            $response = Http::withHeaders([
                'Authorization' => $this->token,
                'Content-Type' => 'application/json',
            ])
            ->withoutVerifying()
            ->timeout(5)
            ->post($url, empty($payload) ? (object)[] : $payload);

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error("MAX answerCallback exception: " . $e->getMessage());
            return false;
        }
    }
}

