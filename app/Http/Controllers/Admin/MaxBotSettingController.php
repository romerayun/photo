<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaxBotSettingController extends Controller
{
    /**
     * Display the MAX bot configuration form.
     */
    public function index(): View
    {
        $settings = [
            // Standard button toggles
            'btn_card_enabled' => Setting::get('max_bot_btn_card_enabled', '1') === '1',
            'btn_card_text' => Setting::get('max_bot_btn_card_text', '📱 Открыть карточку съёмки'),

            'btn_details_enabled' => Setting::get('max_bot_btn_details_enabled', '1') === '1',
            'btn_details_text' => Setting::get('max_bot_btn_details_text', 'ℹ️ Детали съёмки'),

            'btn_tips_enabled' => Setting::get('max_bot_btn_tips_enabled', '1') === '1',
            'btn_tips_text' => Setting::get('max_bot_btn_tips_text', '👗 Подготовка'),
            'tips_response' => Setting::get('max_bot_tips_response', 
                "💡 *Памятка подготовки к съёмке*:\n\n"
                . "1. 🕒 *Время*: Пожалуйста, приезжайте за 10–15 минут до начала, чтобы без спешки переодеться и подготовиться.\n"
                . "2. 👗 *Одежда*: Возьмите чистую сменную обувь (для студии) и заранее отпарьте вещи.\n"
                . "3. 💄 *Макияж и прическа*: Если делаете образ у стилиста, заложите достаточно времени на сборы.\n"
                . "4. 😴 *Отдых*: Постарайтесь хорошо выспаться и не пить много воды на ночь.\n\n"
                . "Если у вас есть вопросы по референсам или идеям — пишите фотографу!"
            ),

            'btn_contacts_enabled' => Setting::get('max_bot_btn_contacts_enabled', '1') === '1',
            'btn_contacts_text' => Setting::get('max_bot_btn_contacts_text', '📞 Контакты фотографа'),
            'contacts_response' => Setting::get('max_bot_contacts_response',
                "📸 *Контакты фотографа (Роман Юн)*:\n\n"
                . "📞 Телефон: " . (Setting::get('phone') ?: '+7 (900) 000-00-00') . "\n"
                . "🌐 Сайт: " . url('/') . "\n"
                . "📷 Портфолио: " . url('/portfolio') . "\n\n"
                . "Всегда на связи и готов ответить на любые вопросы!"
            ),

            'welcome_text' => Setting::get('max_bot_welcome_text', 
                "Здравствуйте, {client_name}! 👋\n\n"
                . "Напоминания о вашей фотосессии успешно подключены в MAX.\n\n"
                . "📅 Дата: {date}\n"
                . "⏰ Время: {time}\n"
                . "{location}\n"
                . "Вы можете воспользоваться кнопками ниже для быстрой информации:"
            ),

            'custom_buttons' => json_decode(Setting::get('max_bot_custom_buttons', '[]'), true) ?: [],
        ];

        return view('admin.max_bot.index', compact('settings'));
    }

    /**
     * Update the MAX bot configuration.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'btn_card_text' => ['required', 'string', 'max:50'],
            'btn_details_text' => ['required', 'string', 'max:50'],
            'btn_tips_text' => ['required', 'string', 'max:50'],
            'tips_response' => ['required', 'string', 'max:2000'],
            'btn_contacts_text' => ['required', 'string', 'max:50'],
            'contacts_response' => ['required', 'string', 'max:2000'],
            'welcome_text' => ['required', 'string', 'max:1000'],
            'custom_buttons' => ['nullable', 'array'],
            'custom_buttons.*.title' => ['required_with:custom_buttons', 'string', 'max:50'],
            'custom_buttons.*.type' => ['required_with:custom_buttons', 'in:link,text'],
            'custom_buttons.*.url' => ['nullable', 'string', 'max:255'],
            'custom_buttons.*.reply' => ['nullable', 'string', 'max:2000'],
        ]);

        Setting::set('max_bot_btn_card_enabled', $request->has('btn_card_enabled') ? '1' : '0');
        Setting::set('max_bot_btn_card_text', $validated['btn_card_text']);

        Setting::set('max_bot_btn_details_enabled', $request->has('btn_details_enabled') ? '1' : '0');
        Setting::set('max_bot_btn_details_text', $validated['btn_details_text']);

        Setting::set('max_bot_btn_tips_enabled', $request->has('btn_tips_enabled') ? '1' : '0');
        Setting::set('max_bot_btn_tips_text', $validated['btn_tips_text']);
        Setting::set('max_bot_tips_response', $validated['tips_response']);

        Setting::set('max_bot_btn_contacts_enabled', $request->has('btn_contacts_enabled') ? '1' : '0');
        Setting::set('max_bot_btn_contacts_text', $validated['btn_contacts_text']);
        Setting::set('max_bot_contacts_response', $validated['contacts_response']);

        Setting::set('max_bot_welcome_text', $validated['welcome_text']);

        // Filter and sanitize custom buttons
        $customButtons = [];
        if (!empty($validated['custom_buttons'])) {
            foreach ($validated['custom_buttons'] as $btn) {
                $title = trim($btn['title'] ?? '');
                if (empty($title)) {
                    continue;
                }
                $customButtons[] = [
                    'title' => $title,
                    'type' => $btn['type'] ?? 'link',
                    'url' => trim($btn['url'] ?? ''),
                    'reply' => trim($btn['reply'] ?? ''),
                ];
            }
        }
        Setting::set('max_bot_custom_buttons', json_encode(array_values($customButtons), JSON_UNESCAPED_UNICODE));

        return back()->with('success', 'Настройки кнопок и текстов бота MAX успешно сохранены.');
    }

    /**
     * Display connected clients and broadcast form.
     */
    public function clients(): View
    {
        // Get all shoots that have a connected MAX user or chat
        $shoots = \App\Models\Shoot::whereNotNull('max_connected_at')
            ->where(function ($query) {
                $query->whereNotNull('max_chat_id')
                      ->orWhereNotNull('max_user_id');
            })
            ->latest('max_connected_at')
            ->get();

        // Count unique subscribers by max_chat_id / max_user_id
        $uniqueSubscribersCount = $shoots->unique(function ($item) {
            return $item->max_chat_id ?: $item->max_user_id;
        })->count();

        // Get configured buttons available for attachment
        $availableButtons = [
            'card' => Setting::get('max_bot_btn_card_text', '📱 Открыть карточку съёмки'),
            'details' => Setting::get('max_bot_btn_details_text', 'ℹ️ Детали съёмки'),
            'tips' => Setting::get('max_bot_btn_tips_text', '👗 Подготовка'),
            'contacts' => Setting::get('max_bot_btn_contacts_text', '📞 Контакты фотографа'),
        ];

        $customButtons = json_decode(Setting::get('max_bot_custom_buttons', '[]'), true) ?: [];
        foreach ($customButtons as $index => $cBtn) {
            if (!empty($cBtn['title'])) {
                $availableButtons["custom_{$index}"] = $cBtn['title'];
            }
        }

        return view('admin.max_bot.clients', compact('shoots', 'uniqueSubscribersCount', 'availableButtons'));
    }

    /**
     * Send broadcast message to all or selected connected clients.
     */
    public function sendBroadcast(Request $request, \App\Services\MaxMessengerService $maxService): RedirectResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:3000'],
            'target' => ['required', 'in:all,selected'],
            'shoot_ids' => ['nullable', 'array'],
            'shoot_ids.*' => ['exists:shoots,id'],
            'attach_buttons' => ['nullable', 'in:none,all,custom'],
            'selected_buttons' => ['nullable', 'array'],
            'selected_buttons.*' => ['string'],
        ]);

        $query = \App\Models\Shoot::whereNotNull('max_connected_at')
            ->where(function ($q) {
                $q->whereNotNull('max_chat_id')
                  ->orWhereNotNull('max_user_id');
            });

        if ($validated['target'] === 'selected') {
            if (empty($validated['shoot_ids'])) {
                return back()->withErrors(['shoot_ids' => 'Выберите хотя бы одного получателя для рассылки.']);
            }
            $query->whereIn('id', $validated['shoot_ids']);
        }

        $recipients = $query->get();

        if ($recipients->isEmpty()) {
            return back()->withErrors(['message' => 'Нет активных получателей в MAX для отправки.']);
        }

        $attachMode = $validated['attach_buttons'] ?? 'none';
        $selectedButtons = $validated['selected_buttons'] ?? [];

        $sentCount = 0;
        $failedCount = 0;
        $processedRecipients = [];

        foreach ($recipients as $recipient) {
            $uniqueKey = $recipient->max_chat_id ?: $recipient->max_user_id;
            if (isset($processedRecipients[$uniqueKey])) {
                continue; // Avoid sending duplicate message to the same chat
            }
            $processedRecipients[$uniqueKey] = true;

            $text = str_replace('{client_name}', $recipient->client_name, $validated['message']);
            
            $buttons = [];
            if ($attachMode === 'all') {
                $buttons = $maxService->getMenuButtons($recipient);
            } elseif ($attachMode === 'custom' && !empty($selectedButtons)) {
                $buttons = $maxService->getMenuButtons($recipient, $selectedButtons);
            }

            $success = $maxService->sendMessage($recipient->max_chat_id, $recipient->max_user_id, $text, $buttons);

            if ($success) {
                $sentCount++;
            } else {
                $failedCount++;
            }

            // Small delay to prevent rate limiting
            usleep(150000); // 150ms
        }

        $statusMsg = "Рассылка завершена! Успешно доставлено: {$sentCount}";
        if ($failedCount > 0) {
            $statusMsg .= ", ошибок: {$failedCount}";
        }

        return back()->with('success', $statusMsg);
    }
}
