<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->segment(1) === 'bn' ? 'bn' : 'en';

        if ($locale === 'bn') {
            App::setLocale('bn');
            Session::put('locale', 'bn');
        } else {
            App::setLocale('en');
            Session::put('locale', 'en');
        }

        return $next($request);
    }
}
