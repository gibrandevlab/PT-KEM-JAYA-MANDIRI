<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ExpireRememberedSession
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasSession() && $request->session()->has('auth.last_activity')) {
            $lastActivity = (int) $request->session()->get('auth.last_activity');

            if ($lastActivity < now()->subDays(7)->timestamp) {
                $user = $request->user();

                if ($user instanceof User) {
                    $user->setRememberToken(null);
                    $user->save();
                }

                auth()->logout();
                $request->session()->forget(['auth.remembered', 'auth.last_activity']);
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login');
            }
        }

        if ($request->user() && $request->session()->get('auth.remembered')) {
            $request->session()->put('auth.last_activity', now()->timestamp);
        }

        return $next($request);
    }
}
