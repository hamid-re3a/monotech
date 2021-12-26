<?php

namespace User\Http\Middlewares;


use Closure;

class TokenHasPassed2FAMiddleware
{

    public function handle($request, Closure $next)
    {

        if(!auth()->check() || !auth()->user()->google2fa_enable || auth()->user()->tokenCan('hasPassed:2fa'))
            return $next($request);
        return api()->error(trans('user.responses.unauthorized'),[],472);
    }
}
