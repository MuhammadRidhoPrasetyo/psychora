<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->activeSubscription) {
            if ($request->header('X-Inertia')) {
                return redirect()->route('subscription.plans')
                    ->with('error', 'Anda memerlukan subscription aktif untuk mengakses fitur ini.');
            }

            abort(403, 'Active subscription required.');
        }

        return $next($request);
    }
}
