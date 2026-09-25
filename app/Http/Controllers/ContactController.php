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

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'telegram' => ['nullable', 'string', 'max:100'],
            'package' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:3000'],
        ], [
            'name.required' => 'Пожалуйста, укажите ваше имя.',
            'email.required' => 'Пожалуйста, укажите email для ответа.',
            'email.email' => 'Пожалуйста, укажите корректный email адрес.',
            'message.required' => 'Пожалуйста, напишите пару слов о желаемой съёмке.',
        ]);

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
