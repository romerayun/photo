<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Package;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function index(Request $request, string $locale): View
    {
        $isDemo = Setting::isDemoMode();

        $packages = Package::query()
            ->published()
            ->get();

        $faqs = Faq::query()
            ->active()
            ->get();

        return view('pages.pricing', [
            'packages' => $packages,
            'faqs' => $faqs,
            'isDemo' => $isDemo,
            'locale' => $locale,
        ]);
    }
}
