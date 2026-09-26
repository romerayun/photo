<?php

namespace App\Http\Controllers;

use App\Mail\ContactFormSubmitted;
use App\Models\Package;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(Request $request): View
    {
        $locale = 'ru';
        $isDemo = Setting::isDemoMode();

        $selectedPackage = trim((string) $request->query('package', ''));

        $telegramUrl = Setting::telegramUrl($selectedPackage ? "Здравствуйте, Роман! Хочу обсудить съёмку по пакету «{$selectedPackage}»." : null);
        $telegramDefaultUrl = Setting::telegramUrl();
        $telegramHandle = Setting::telegramHandle();
        $phoneLink = Setting::phoneLink();
        $phoneDisplay = Setting::phoneDisplay();
        $city = Setting::get('city_ru', 'Иркутск');

        $packages = Package::query()
            ->published()
            ->get();

        return view('pages.contacts', [
            'telegramUrl' => $telegramUrl,
            'telegramDefaultUrl' => $telegramDefaultUrl,
            'telegramHandle' => $telegramHandle,
            'phoneLink' => $phoneLink,
            'phoneDisplay' => $phoneDisplay,
            'city' => $city,
            'isDemo' => $isDemo,
            'locale' => $locale,
            'selectedPackage' => $selectedPackage,
            'packages' => $packages,
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        // Anti-spam honeypot
        if (!empty($request->input('_hp'))) {
            return redirect()->route('contacts.index')
                ->with('contact_success', 'Спасибо за обращение! Ваша заявка успешно принята. Отвечу вам в течение 1–2 часов.');
        }

        // Backward compatibility: if legacy email is submitted without contact_method
        if (!$request->has('contact_method') && $request->filled('email')) {
            $request->merge([
                'contact_method' => 'email',
                'contact_value' => $request->input('email'),
            ]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'contact_method' => ['required', 'string', 'in:phone,telegram,max,email'],
            'contact_value' => ['required', 'string', 'max:150'],
            'package' => ['nullable', 'string', 'max:150'],
            'message' => ['nullable', 'string', 'max:3000'],
            'email' => ['nullable', 'email', 'max:150'],
        ], [
            'name.required' => 'Пожалуйста, укажите ваше имя.',
            'contact_method.required' => 'Пожалуйста, выберите удобный способ связи.',
            'contact_value.required' => 'Пожалуйста, укажите контактные данные для выбранного способа связи.',
            'email.email' => 'Пожалуйста, укажите корректный email адрес.',
        ]);

        // If email was explicitly provided with invalid syntax (legacy or modern)
        if ($request->filled('email') && !filter_var($request->input('email'), FILTER_VALIDATE_EMAIL)) {
            return back()->withInput()->withErrors(['email' => 'Пожалуйста, укажите корректный email адрес.']);
        }

        // Specific format validation for email if chosen as contact_method
        if ($validated['contact_method'] === 'email' && !filter_var($validated['contact_value'], FILTER_VALIDATE_EMAIL)) {
            return back()->withInput()->withErrors([
                'contact_value' => 'Пожалуйста, укажите корректный адрес электронной почты.',
                'email' => 'Пожалуйста, укажите корректный email адрес.',
            ]);
        }

        // Map contact_value back to convenience fields for mail templates and backward compatibility
        $contactMethod = $validated['contact_method'];
        $contactValue = trim($validated['contact_value']);
        $validated[$contactMethod] = $contactValue;
        if (!empty($request->input('phone'))) {
            $validated['phone'] = $request->input('phone');
        }
        if (!empty($request->input('telegram'))) {
            $validated['telegram'] = $request->input('telegram');
        }

        // Friendly label for email
        $methodLabels = [
            'phone' => 'Телефон',
            'telegram' => 'Telegram',
            'max' => 'MAX (Мессенджер)',
            'email' => 'Email',
        ];
        $validated['contact_method_label'] = $methodLabels[$contactMethod] ?? $contactMethod;

        $recipient = Setting::contactEmail();

        try {
            Mail::to($recipient)->send(new ContactFormSubmitted($validated));
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('contact_error', 'Не удалось отправить сообщение. Пожалуйста, напишите напрямую в Telegram или позвоните.');
        }

        $packageName = $validated['package'] ?? null;

        return redirect()->route('contacts.index', array_filter(['package' => $packageName]))
            ->with('contact_success', 'Спасибо за обращение! Ваша заявка успешно отправлена. Отвечу вам в течение 1–2 часов.');
    }
}
