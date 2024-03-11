<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserAndDeliboyMobileToken
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return JsonResponse|RedirectResponse|Response
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user()->tokenCan('userMobileToken') && !$request->user()->tokenCan('deliboyMobileToken')) {
            return response()->json([
                'status' => 'error',
                'message' => "Your Api Token don't have permission."
            ], 403);
        }
        return $next($request);

    }
}
