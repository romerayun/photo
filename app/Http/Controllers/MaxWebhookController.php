<?php

namespace App\Http\Controllers;

use App\Models\Shoot;
use App\Services\MaxMessengerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaxWebhookController extends Controller
{
    /**
     * Handle incoming webhook from MAX messenger platform.
     * Expected event: bot_started (or message_created with payload / start parameter).
     */
    public function handle(Request $request, MaxMessengerService $maxService): JsonResponse
    {
        Log::info('MAX Webhook received payload:', $request->all());

        // Extract event data according to MAX Bot API formats
        $event = $request->input('update_type') ?? $request->input('event') ?? $request->input('type');
        
        // 1. Extract payload code
        // MAX format:
        // {
        //   "update_type": "bot_started",
        //   "chat_id": 1234567890,
        //   "user": { "user_id": 1234567890, ... },
        //   "payload": "code"
        // }
        $payload = $request->input('payload') 
            ?? $request->input('data.payload')
            ?? $request->input('data.start_payload')
            ?? $request->input('message.payload');

        // Check if code was passed in text like "/start <code>"
        if (!$payload) {
            $text = $request->input('message.text') ?? $request->input('text') ?? '';
            if (preg_match('/^\/start\s+([a-zA-Z0-9_-]+)/', trim($text), $matches)) {
                $payload = $matches[1];
            }
        }

        // 2. Extract user_id and chat_id
        $userId = $request->input('user.user_id') 
            ?? $request->input('user_id') 
            ?? $request->input('data.user.user_id') 
            ?? $request->input('message.from.id')
            ?? $request->input('sender.id');

        $chatId = $request->input('chat_id') 
            ?? $request->input('data.chat_id') 
            ?? $request->input('message.chat.id')
            ?? $request->input('chat.id')
            ?? $userId;

        // Handle interactive callback button clicks (update_type: message_callback)
        if ($event === 'message_callback' || $request->has('callback')) {
            return $this->handleCallback($request, $maxService, (string)$chatId, (string)$userId);
        }

        if (!$payload) {
            // Check if user just texted the bot (e.g. "инфо", "меню", "помощь")
            $text = trim($request->input('message.text') ?? $request->input('text') ?? '');
            if (!empty($text)) {
                return $this->handleTextMessage($text, $maxService, (string)$chatId, (string)$userId);
            }

            return response()->json([
                'status' => 'ignored',
                'message' => 'No start payload or recognized action found in event',
            ], 200);
        }

        $codeHash = hash('sha256', trim($payload));

        // 3. Atomically find shoot, check expiration, redeem code, and link MAX user
        $shoot = DB::transaction(function () use ($codeHash, $userId, $chatId) {
            $shootRecord = Shoot::where('max_link_code_hash', $codeHash)
                ->where('max_link_code_expires_at', '>=', now())
                ->lockForUpdate()
                ->first();

            if (!$shootRecord) {
                return null;
            }

            // Atomically invalidate code and save MAX identifiers
            $shootRecord->update([
                'max_link_code_hash' => null,
                'max_link_code_expires_at' => null,
                'max_user_id' => $userId ? (string)$userId : null,
                'max_chat_id' => $chatId ? (string)$chatId : null,
                'max_connected_at' => now(),
            ]);

            return $shootRecord;
        });

        if (!$shoot) {
            Log::warning("MAX Webhook: invalid or expired start code '{$payload}'");
            return response()->json([
                'status' => 'error',
                'message' => 'Code invalid or expired',
            ], 400);
        }

        // 4. Send confirmation message with interactive buttons
        $maxService->sendConfirmation($shoot);

        Log::info("MAX linked successfully to shoot ID #{$shoot->id} for user {$userId}");

        return response()->json([
            'status' => 'ok',
            'shoot_id' => $shoot->id,
            'message' => 'Shoot successfully linked to MAX and confirmation sent',
        ]);
    }

    /**
     * Handle button clicks (message_callback event).
     */
    protected function handleCallback(Request $request, MaxMessengerService $maxService, string $chatId, string $userId): JsonResponse
    {
        $callbackPayload = $request->input('callback.payload') 
            ?? $request->input('payload') 
            ?? $request->input('data.payload');

        // Find linked shoot for this user or chat
        $shoot = Shoot::where('max_chat_id', $chatId)
            ->orWhere('max_user_id', $userId)
            ->latest('shoot_date')
            ->first();

        if ($callbackPayload === 'shoot_details') {
            if (!$shoot) {
                $maxService->sendMessage($chatId, $userId, "У вас пока нет активной фотосессии, привязанной к этому чату.");
                return response()->json(['status' => 'ok']);
            }

            $dateFormatted = $shoot->shoot_date ? $shoot->shoot_date->format('d.m.Y') : 'Дата не указана';
            $time = substr($shoot->start_time, 0, 5) ?: 'Время не указано';
            $duration = $shoot->duration_minutes ? "{$shoot->duration_minutes} мин." : '';
            $statusLabels = [
                'planned' => 'Запланирована',
                'in_progress' => 'В процессе',
                'completed' => 'Завершена',
                'cancelled' => 'Отменена',
            ];
            $status = $statusLabels[$shoot->status] ?? $shoot->status;

            $text = "📋 *Детали вашей фотосессии*:\n\n"
                . "👤 Клиент: {$shoot->client_name}\n"
                . "📅 Дата: {$dateFormatted}\n"
                . "⏰ Время: {$time} " . ($duration ? "({$duration})" : '') . "\n"
                . "📍 Локация: " . ($shoot->location ?: 'уточняется') . "\n"
                . "💰 Стоимость: " . ($shoot->price ? number_format($shoot->price, 0, '', ' ') . ' ₽' : '—') . "\n"
                . ($shoot->prepayment ? "💵 Предоплата: " . number_format($shoot->prepayment, 0, '', ' ') . " ₽ (Внесена)\n" : '')
                . "📌 Статус: {$status}";

            $buttons = [];
            if ($shoot->share_token) {
                $buttons[] = [
                    ['type' => 'link', 'text' => '📱 Открыть веб-карточку', 'url' => url("/shoot/{$shoot->share_token}")],
                ];
            }
            $buttons[] = [
                ['type' => 'callback', 'text' => '👗 Подготовка', 'payload' => 'shoot_tips'],
                ['type' => 'callback', 'text' => '📞 Контакты', 'payload' => 'photographer_contacts'],
            ];

            $maxService->sendMessage($chatId, $userId, $text, $buttons);
            return response()->json(['status' => 'ok']);
        }

        if ($callbackPayload === 'shoot_tips') {
            $text = "💡 *Памятка подготовки к съёмке*:\n\n"
                . "1. 🕒 *Время*: Пожалуйста, приезжайте за 10–15 минут до начала, чтобы без спешки переодеться и подготовиться.\n"
                . "2. 👗 *Одежда*: Возьмите чистую сменную обувь (для студии) и заранее отпарьте вещи.\n"
                . "3. 💄 *Макияж и прическа*: Если делаете образ у стилиста, заложите достаточно времени на сборы.\n"
                . "4. 😴 *Отдых*: Постарайтесь хорошо выспаться и не пить много воды на ночь.\n\n"
                . "Если у вас есть вопросы по референсам или идеям — пишите фотографу!";

            $buttons = [
                [
                    ['type' => 'callback', 'text' => 'ℹ️ Детали съёмки', 'payload' => 'shoot_details'],
                    ['type' => 'callback', 'text' => '📞 Контакты', 'payload' => 'photographer_contacts'],
                ],
            ];

            $maxService->sendMessage($chatId, $userId, $text, $buttons);
            return response()->json(['status' => 'ok']);
        }

        if ($callbackPayload === 'photographer_contacts') {
            $text = "📸 *Контакты фотографа (Роман Юн)*:\n\n"
                . "📞 Телефон: +7 (900) 000-00-00\n"
                . "🌐 Сайт: " . url('/') . "\n"
                . "📷 Портфолио: " . url('/series') . "\n\n"
                . "Всегда на связи и готов ответить на любые вопросы!";

            $buttons = [
                [
                    ['type' => 'link', 'text' => '🌐 Перейти на сайт', 'url' => url('/')],
                ],
                [
                    ['type' => 'callback', 'text' => 'ℹ️ Детали съёмки', 'payload' => 'shoot_details'],
                ],
            ];

            $maxService->sendMessage($chatId, $userId, $text, $buttons);
            return response()->json(['status' => 'ok']);
        }

        return response()->json(['status' => 'ignored']);
    }

    /**
     * Handle incoming text messages (e.g. client sends "привет", "инфо", "меню").
     */
    protected function handleTextMessage(string $text, MaxMessengerService $maxService, string $chatId, string $userId): JsonResponse
    {
        $shoot = Shoot::where('max_chat_id', $chatId)
            ->orWhere('max_user_id', $userId)
            ->latest('shoot_date')
            ->first();

        $reply = "Здравствуйте! Чем могу помочь? Выберите действие кнопками ниже:";

        $buttons = [];
        if ($shoot && $shoot->share_token) {
            $buttons[] = [
                ['type' => 'link', 'text' => '📱 Моя карточка съёмки', 'url' => url("/shoot/{$shoot->share_token}")],
            ];
        }

        $buttons[] = [
            ['type' => 'callback', 'text' => 'ℹ️ Детали съёмки', 'payload' => 'shoot_details'],
            ['type' => 'callback', 'text' => '👗 Подготовка к съёмке', 'payload' => 'shoot_tips'],
        ];
        $buttons[] = [
            ['type' => 'callback', 'text' => '📞 Контакты фотографа', 'payload' => 'photographer_contacts'],
        ];

        $maxService->sendMessage($chatId, $userId, $reply, $buttons);

        return response()->json(['status' => 'ok']);
    }
}
