<?php

namespace App\Http\Middleware;

use App\Traits\HttpResponses;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
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
            if (auth()->user()->is_admin) {
                return $next($request);
            } else {
                return $this->error(
                    message: "Siz admin istifadəçi deyilsiniz",
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
