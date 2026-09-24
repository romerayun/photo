<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function index(Request $request, string $locale): View
    {
        $isDemo = Setting::isDemoMode();

        return view('pages.about', [
            'isDemo' => $isDemo,
            'locale' => $locale,
        ]);
    }
}
