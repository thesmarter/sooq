<?php

namespace App\Http\Middleware;

use App\Http\Services\PsService;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Core\Entities\Project;

class SystemCode2
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
