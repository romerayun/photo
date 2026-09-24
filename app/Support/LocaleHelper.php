<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

class LocaleHelper
{
    public static function alternateUrl(string $targetLocale): string
    {
        $currentRoute = Route::current();
        if (!$currentRoute) {
            return url("/{$targetLocale}");
        }

        $parameters = $currentRoute->parameters();
        $parameters['locale'] = $targetLocale;

        try {
            $routeName = $currentRoute->getName();
            if ($routeName) {
                return route($routeName, $parameters);
            }
        } catch (\Throwable $e) {
            // Fallback
        }

        return url("/{$targetLocale}");
    }
}
