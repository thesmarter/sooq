<?php

namespace Modules\Installer\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Installer\Helpers\MigrationsHelper;

class canUpdate
{
    use MigrationsHelper;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
