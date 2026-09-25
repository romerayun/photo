<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Series;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = [
            'telegram' => Setting::get('telegram', ''),
            'phone' => Setting::get('phone', ''),
            'city_ru' => Setting::get('city_ru', 'Иркутск'),
            'city_en' => Setting::get('city_en', 'Irkutsk'),
            'hero_phrase_ru' => Setting::get('hero_phrase_ru', 'Ваши истории. Мой взгляд.'),
            'hero_phrase_en' => Setting::get('hero_phrase_en', 'Your stories. My perspective.'),
            'hero_sub_ru' => Setting::get('hero_sub_ru', 'Портреты, съёмки для пар и семей, события и контент для бизнеса. Иркутск.'),
            'hero_sub_en' => Setting::get('hero_sub_en', 'Portraits, sessions for couples and families, events, and business content. Irkutsk.'),
            'demo_mode' => Setting::isDemoMode() ? '1' : '0',
            'contact_email' => Setting::contactEmail(),
            'home_spotlight_series_id' => Setting::get('home_spotlight_series_id', ''),
        ];

        $seriesList = Series::with('category')->orderBy('sort_order')->get();

        return view('admin.settings.index', [
            'settings' => $settings,
            'seriesList' => $seriesList,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'telegram' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'contact_email' => ['nullable', 'email', 'max:150'],
            'city_ru' => ['required', 'string', 'max:100'],
            'city_en' => ['nullable', 'string', 'max:100'],
            'hero_phrase_ru' => ['required', 'string', 'max:255'],
            'hero_phrase_en' => ['nullable', 'string', 'max:255'],
            'hero_sub_ru' => ['nullable', 'string'],
            'hero_sub_en' => ['nullable', 'string'],
            'demo_mode' => ['nullable'],
            'home_spotlight_series_id' => ['nullable', 'exists:series,id'],
        ]);

        Setting::set('telegram', $validated['telegram'] ?? '');
        Setting::set('phone', $validated['phone'] ?? '');
        Setting::set('contact_email', !empty($validated['contact_email']) ? $validated['contact_email'] : 'romerayun@gmail.com');
        Setting::set('city_ru', $validated['city_ru']);
        if (!empty($validated['city_en'])) {
            Setting::set('city_en', $validated['city_en']);
        }
        Setting::set('hero_phrase_ru', $validated['hero_phrase_ru']);
        if (!empty($validated['hero_phrase_en'])) {
            Setting::set('hero_phrase_en', $validated['hero_phrase_en']);
        }
        Setting::set('hero_sub_ru', $validated['hero_sub_ru'] ?? '');
        if (isset($validated['hero_sub_en'])) {
            Setting::set('hero_sub_en', $validated['hero_sub_en'] ?? '');
        }
        Setting::set('demo_mode', $request->has('demo_mode') ? '1' : '0');
        Setting::set('home_spotlight_series_id', $validated['home_spotlight_series_id'] ?? '');

        return back()->with('success', 'Настройки сайта успешно сохранены.');
    }
}
