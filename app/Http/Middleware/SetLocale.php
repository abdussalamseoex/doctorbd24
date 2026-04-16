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
        $locale = $request->route('locale');

        if ($locale === 'bn') {
            App::setLocale('bn');
            Session::put('locale', 'bn');
            \Illuminate\Support\Facades\URL::defaults(['locale' => 'bn']);
        } else {
            App::setLocale('en');
            Session::put('locale', 'en');
            // Do not override default URL generation, we want default URLs to be clean
            \Illuminate\Support\Facades\URL::defaults(['locale' => '']);
        }

        // Forget the parameter so it doesn't get injected into controllers!
        if ($request->route()) {
            $request->route()->forgetParameter('locale');
        }

        return $next($request);
    }
}
