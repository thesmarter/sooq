<?php

namespace Modules\Installer\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Modules\Core\Entities\Installer;
use Modules\Core\Entities\DomainChange;

class canInstall
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return RedirectResponse|mixed
     */
    public function handle($request, Closure $next)
    {
        return $next($request);

    }

}
