<?php

namespace App\Http\Middleware;

use App\Traits\HttpResponses;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsFeaturedUser
{
    use HttpResponses;

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            if (auth()->user()->is_featured) {
                return $next($request);
            } else {
                return $this->error(
                    message: "Siz xüsusi istifadəçi deyilsiniz",
                    code: 403
                );
            }
        }

        return $this->error(
            message: "Giriş edin",
            code: 401
        );
    }
}
