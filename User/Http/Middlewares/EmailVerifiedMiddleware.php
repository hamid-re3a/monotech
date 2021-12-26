<?php

namespace User\Http\Middlewares;


use User\Support\Google2FAAuthenticator;
use Closure;

class EmailVerifiedMiddleware
{

    public function handle($request, Closure $next)
    {
        if (auth()->check() && auth()->user()->isEmailVerified()) {
            return $next($request);
        }
        return api()->error(trans('user.responses.unauthorized-email-is-not-verified'),[],403);
    }
}
