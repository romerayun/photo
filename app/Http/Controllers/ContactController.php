<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(Request $request, string $locale): View
    {
        $isDemo = Setting::isDemoMode();

        $telegramUrl = Setting::telegramUrl();
        $telegramHandle = Setting::telegramHandle();
        $phoneLink = Setting::phoneLink();
        $phoneDisplay = Setting::phoneDisplay();
        $city = $locale === 'en' ? Setting::get('city_en', 'Irkutsk') : Setting::get('city_ru', 'Иркутск');

        return view('pages.contacts', [
            'telegramUrl' => $telegramUrl,
            'telegramHandle' => $telegramHandle,
            'phoneLink' => $phoneLink,
            'phoneDisplay' => $phoneDisplay,
            'city' => $city,
            'isDemo' => $isDemo,
            'locale' => $locale,
        ]);
    }
}
