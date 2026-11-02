<?php

namespace App\Ship\Middleware;

use App\Ship\Parents\Middleware\Middleware as ParentMiddleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsurePlatformAdmin extends ParentMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user()?->is_platform_admin, 403, 'This action is unauthorized.');

        return $next($request);
    }
}
