<?php

namespace User\Http\Middlewares;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

class MaintenanceModeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = null;
        if (auth()->check()) {
            $user = auth()->user();

            if($user->roles()->count() == 1 AND $user->hasRole(USER_ROLE_CLIENT) AND $this->systemIsUnderMaintenance())
                $user->tokens()->delete();
                return api()->error(null,[
                    'subject' => trans('user.responses.we-are-under-maintenance')
                ],406);

        }
        return $next($request);
    }



    private function systemIsUnderMaintenance()
    {

        $registration_block_from_date = getSetting('SYSTEM_IS_UNDER_MAINTENANCE_FROM_DATE');
        $registration_block_from_date = strtotime($registration_block_from_date) ? Carbon::parse($registration_block_from_date) : null;

        $registration_block_to_date = getSetting('SYSTEM_IS_UNDER_MAINTENANCE_TO_DATE');
        $registration_block_to_date = strtotime($registration_block_to_date) ? Carbon::parse($registration_block_to_date) : null;

        $response = false;

        if(!empty($registration_block_from_date) OR !empty($registration_block_to_date)) {

            if(!empty($registration_block_from_date) AND !empty($registration_block_to_date))
                $response = Carbon::now()->between($registration_block_from_date,$registration_block_to_date);

            if(!empty($registration_block_from_date) AND empty($registration_block_to_date) AND $registration_block_from_date->isPast())
                $response = true;

            if(empty($registration_block_from_date) AND !empty($registration_block_to_date) AND !$registration_block_to_date->isPast())
                $response = true;
        }

        return $response;
    }


}
