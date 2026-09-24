<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(Request $request): View
    {
        $locale = 'ru';
        $isDemo = Setting::isDemoMode();

        $telegramUrl = Setting::telegramUrl();
        $telegramHandle = Setting::telegramHandle();
        $phoneLink = Setting::phoneLink();
        $phoneDisplay = Setting::phoneDisplay();
        $city = Setting::get('city_ru', 'Иркутск');

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
