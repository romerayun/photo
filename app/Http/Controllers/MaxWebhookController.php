<?php

namespace App\Http\Controllers;

use App\Models\Client;
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

        // 3. Atomically find shoot, check expiration, redeem code, and link MAX user & client
        $shoot = DB::transaction(function () use ($codeHash, $userId, $chatId) {
            $shootRecord = Shoot::where('max_link_code_hash', $codeHash)
                ->where('max_link_code_expires_at', '>=', now())
                ->lockForUpdate()
                ->first();

            if (!$shootRecord) {
                return null;
            }

            // Atomically invalidate code and save MAX identifiers on shoot
            $shootRecord->update([
                'max_link_code_hash' => null,
                'max_link_code_expires_at' => null,
                'max_user_id' => $userId ? (string)$userId : null,
                'max_chat_id' => $chatId ? (string)$chatId : null,
                'max_connected_at' => now(),
            ]);

            // Sync with Client entity
            $client = null;
            if ($shootRecord->client_id) {
                $client = Client::find($shootRecord->client_id);
            }
            if (!$client) {
                // Find or create client by phone or name
                if ($shootRecord->phone) {
                    $client = Client::where('phone', $shootRecord->phone)->first();
                }
                if (!$client && $shootRecord->client_name) {
                    $client = Client::where('name', $shootRecord->client_name)->first();
                }
                if (!$client) {
                    $client = Client::create([
                        'name' => $shootRecord->client_name,
                        'phone' => $shootRecord->phone,
                        'social_link' => $shootRecord->social_link,
                    ]);
                }
                $shootRecord->update(['client_id' => $client->id]);
            }

            if ($client) {
                $client->update([
                    'max_user_id' => $userId ? (string)$userId : $client->max_user_id,
                    'max_chat_id' => $chatId ? (string)$chatId : $client->max_chat_id,
                    'max_connected_at' => now(),
                ]);

                // Also make sure other shoots of this client have MAX ids updated if empty
                Shoot::where('client_id', $client->id)
                    ->whereNull('max_connected_at')
                    ->update([
                        'max_user_id' => $userId ? (string)$userId : null,
                        'max_chat_id' => $chatId ? (string)$chatId : null,
                        'max_connected_at' => now(),
                    ]);
            }

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
        $callbackId = $request->input('callback.callback_id')
            ?? $request->input('callback_id')
            ?? $request->input('data.callback_id');

        if ($callbackId) {
            $maxService->answerCallback($callbackId);
        }

        $callbackPayload = $request->input('callback.payload') 
            ?? $request->input('callback.data')
            ?? $request->input('payload') 
            ?? $request->input('data.payload');

        // Check user/chat in nested structures if empty
        if (empty($chatId) || $chatId === '0') {
            $chatId = (string)($request->input('message.chat_id') 
                ?? $request->input('message.chat.id') 
                ?? $request->input('chat_id') 
                ?? $userId);
        }

        if (empty($userId) || $userId === '0') {
            $userId = (string)($request->input('user.user_id') 
                ?? $request->input('callback.user.user_id') 
                ?? $request->input('user_id'));
        }

        // Find all linked shoots for this user or chat
        $shootsQuery = Shoot::where(function ($query) use ($chatId, $userId) {
                if ($chatId) {
                    $query->where('max_chat_id', $chatId);
                }
                if ($userId) {
                    $query->orWhere('max_user_id', $userId);
                }
            });

        // Also check if linked via Client record
        $client = Client::where(function ($q) use ($chatId, $userId) {
            if ($chatId) {
                $q->where('max_chat_id', $chatId);
            }
            if ($userId) {
                $q->orWhere('max_user_id', $userId);
            }
        })->first();

        if ($client) {
            $shootsQuery->orWhere('client_id', $client->id);
        }

        $allShoots = $shootsQuery
            ->orderBy('shoot_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->get();

        $shoot = $allShoots->first();

        // 1. Details button
        if ($callbackPayload === 'shoot_details') {
            if ($allShoots->isEmpty()) {
                $maxService->sendMessage($chatId, $userId, "У вас пока нет активной фотосессии, привязанной к этому чату.", $maxService->getMenuButtons());
                return response()->json(['status' => 'ok']);
            }

            // If user has MULTIPLE shoots, prompt them to choose which one
            if ($allShoots->count() > 1) {
                $text = "📅 *У вас запланировано несколько съёмок*:\n\nПожалуйста, выберите фотосессию, по которой хотите посмотреть подробную информацию:";
                
                $selectButtons = [];
                foreach ($allShoots as $s) {
                    $dateStr = $s->shoot_date ? $s->shoot_date->format('d.m.Y') : 'Без даты';
                    $locStr = $s->location ? " ({$s->location})" : '';
                    $btnTitle = "📅 {$dateStr}{$locStr}";
                    // Limit button text length for MAX API
                    if (mb_strlen($btnTitle) > 36) {
                        $btnTitle = mb_substr($btnTitle, 0, 35) . '…';
                    }
                    $selectButtons[] = [
                        [
                            'type' => 'callback',
                            'text' => $btnTitle,
                            'payload' => "shoot_select_{$s->id}",
                        ]
                    ];
                }

                // Add back / cancel button
                $selectButtons[] = [
                    [
                        'type' => 'callback',
                        'text' => '🔙 В главное меню',
                        'payload' => 'main_menu',
                    ]
                ];

                $maxService->sendMessage($chatId, $userId, $text, $selectButtons);
                return response()->json(['status' => 'ok']);
            }

            // Exactly 1 shoot
            return $this->sendShootDetailsResponse($maxService, $shoot, $chatId, $userId, $allShoots->count());
        }

        // Specific shoot selected by client from list
        if (preg_match('/^shoot_select_(\d+)$/', $callbackPayload, $matches)) {
            $selectedId = (int)$matches[1];
            $targetShoot = $allShoots->firstWhere('id', $selectedId) ?? Shoot::find($selectedId);

            if ($targetShoot) {
                return $this->sendShootDetailsResponse($maxService, $targetShoot, $chatId, $userId, $allShoots->count());
            }
        }

        // Card button clicked when client has MULTIPLE shoots
        if ($callbackPayload === 'shoot_card_menu') {
            if ($allShoots->isEmpty()) {
                $maxService->sendMessage($chatId, $userId, "У вас пока нет активной фотосессии, привязанной к этому чату.", $maxService->getMenuButtons());
                return response()->json(['status' => 'ok']);
            }

            $text = "📱 *Выберите карточку съёмки*:\n\nУ вас несколько фотосессий. Выберите, какую карточку вы хотите открыть:";
            $cardButtons = [];
            foreach ($allShoots as $s) {
                $dateStr = $s->shoot_date ? $s->shoot_date->format('d.m.Y') : 'Без даты';
                $locStr = $s->location ? " ({$s->location})" : '';
                $btnTitle = "📱 {$dateStr}{$locStr}";
                if (mb_strlen($btnTitle) > 36) {
                    $btnTitle = mb_substr($btnTitle, 0, 35) . '…';
                }
                $cardButtons[] = [
                    [
                        'type' => 'link',
                        'text' => $btnTitle,
                        'url' => url("/shoot/{$s->share_token}"),
                    ]
                ];
            }

            // Back button
            $cardButtons[] = [
                [
                    'type' => 'callback',
                    'text' => '🔙 В главное меню',
                    'payload' => 'main_menu',
                ]
            ];

            $maxService->sendMessage($chatId, $userId, $text, $cardButtons);
            return response()->json(['status' => 'ok']);
        }

        // Main menu button
        if ($callbackPayload === 'main_menu') {
            $reply = "Главное меню бота. Выберите действие кнопками ниже:";
            $buttons = $maxService->getMenuButtons($shoot, null, $allShoots->count());
            $maxService->sendMessage($chatId, $userId, $reply, $buttons);
            return response()->json(['status' => 'ok']);
        }

        // 2. Tips button
        if ($callbackPayload === 'shoot_tips') {
            $text = \App\Models\Setting::get('max_bot_tips_response', 
                "💡 *Памятка подготовки к съёмке*:\n\n"
                . "1. 🕒 *Время*: Пожалуйста, приезжайте за 10–15 минут до начала, чтобы без спешки переодеться и подготовиться.\n"
                . "2. 👗 *Одежда*: Возьмите чистую сменную обувь (для студии) и заранее отпарьте вещи.\n"
                . "3. 💄 *Макияж и прическа*: Если делаете образ у стилиста, заложите достаточно времени на сборы.\n"
                . "4. 😴 *Отдых*: Постарайтесь хорошо выспаться и не пить много воды на ночь.\n\n"
                . "Если у вас есть вопросы по референсам или идеям — пишите фотографу!"
            );

            $buttons = $maxService->getMenuButtons($shoot);

            $maxService->sendMessage($chatId, $userId, $text, $buttons);
            return response()->json(['status' => 'ok']);
        }

        // 3. Contacts button
        if ($callbackPayload === 'photographer_contacts') {
            $defaultContacts = "📸 *Контакты фотографа (Роман Юн)*:\n\n"
                . "📞 Телефон: " . (\App\Models\Setting::get('phone') ?: '+7 (900) 000-00-00') . "\n"
                . "🌐 Сайт: " . url('/') . "\n"
                . "📷 Портфолио: " . url('/portfolio') . "\n\n"
                . "Всегда на связи и готов ответить на любые вопросы!";

            $text = \App\Models\Setting::get('max_bot_contacts_response', $defaultContacts);

            $buttons = $maxService->getMenuButtons($shoot);

            $maxService->sendMessage($chatId, $userId, $text, $buttons);
            return response()->json(['status' => 'ok']);
        }

        // 4. Custom button callbacks (custom_btn_0, custom_btn_1, etc.)
        if (preg_match('/^custom_btn_(\d+)$/', $callbackPayload, $matches)) {
            $index = (int)$matches[1];
            $customButtons = json_decode(\App\Models\Setting::get('max_bot_custom_buttons', '[]'), true) ?: [];
            if (isset($customButtons[$index])) {
                $btn = $customButtons[$index];
                $reply = !empty($btn['reply']) ? $btn['reply'] : 'Информация по данному запросу пока не заполнена.';
                $buttons = $maxService->getMenuButtons($shoot);
                $maxService->sendMessage($chatId, $userId, $reply, $buttons);
                return response()->json(['status' => 'ok']);
            }
        }

        return response()->json(['status' => 'ignored']);
    }

    /**
     * Handle incoming text messages (e.g. client sends "привет", "инфо", "меню").
     */
    protected function handleTextMessage(string $text, MaxMessengerService $maxService, string $chatId, string $userId): JsonResponse
    {
        $shootsQuery = Shoot::where(function ($query) use ($chatId, $userId) {
            if ($chatId) $query->where('max_chat_id', $chatId);
            if ($userId) $query->orWhere('max_user_id', $userId);
        });

        $client = Client::where(function ($q) use ($chatId, $userId) {
            if ($chatId) $q->where('max_chat_id', $chatId);
            if ($userId) $q->orWhere('max_user_id', $userId);
        })->first();

        if ($client) {
            $shootsQuery->orWhere('client_id', $client->id);
        }

        $allShoots = $shootsQuery->orderBy('shoot_date', 'desc')->get();
        $shoot = $allShoots->first();

        $reply = "Здравствуйте! Чем могу помочь? Выберите действие кнопками меню ниже:";
        $buttons = $maxService->getMenuButtons($shoot, null, $allShoots->count());

        $maxService->sendMessage($chatId, $userId, $reply, $buttons);

        return response()->json(['status' => 'ok']);
    }

    /**
     * Send formatted shoot details with back/menu buttons.
     */
    protected function sendShootDetailsResponse(MaxMessengerService $maxService, Shoot $shoot, string $chatId, string $userId, int $shootsCount = 1): JsonResponse
    {
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

        $text = "📋 *Детали съёмки*:\n\n"
            . "👤 Клиент: {$shoot->client_name}\n"
            . "📅 Дата: {$dateFormatted}\n"
            . "⏰ Время: {$time} " . ($duration ? "({$duration})" : '') . "\n"
            . "📍 Локация: " . ($shoot->location ?: 'уточняется') . "\n"
            . "💰 Стоимость: " . ($shoot->price ? number_format($shoot->price, 0, '', ' ') . ' ₽' : '—') . "\n"
            . ($shoot->prepayment ? "💵 Предоплата: " . number_format($shoot->prepayment, 0, '', ' ') . " ₽ (Внесена)\n" : '')
            . "📌 Статус: {$status}";

        $buttons = $maxService->getMenuButtons($shoot, null, $shootsCount);

        $maxService->sendMessage($chatId, $userId, $text, $buttons);
        return response()->json(['status' => 'ok']);
    }
}

