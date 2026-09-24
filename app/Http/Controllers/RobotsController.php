<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function index(): Response
    {
        $isDemo = Setting::isDemoMode();

        if ($isDemo) {
            $content = "User-agent: *\nDisallow: /\n# Site is in demonstration mode\n";
        } else {
            $sitemapUrl = url('/sitemap.xml');
            $content = "User-agent: *\nAllow: /\nDisallow: /admin\n\nSitemap: {$sitemapUrl}\n";
        }

        return response($content, 200, [
            'Content-Type' => 'text/plain',
        ]);
    }
}
