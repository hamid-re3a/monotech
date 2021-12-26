<?php

namespace User\Http\Controllers\Front;


use User\Http\Requests\User\session\TerminateSessionRequest;
use User\Http\Resources\User\ActiveSessionsResource;
use Illuminate\Routing\Controller;

class SessionController extends Controller
{
    /**
     * Get All Sessions
     * @group Public User > Session
     */
    public function index()
    {
        $agents = request()->user()->agents()->with('ips')->whereHas('ips')->whereNotNull('token_id')->simplePaginate();
        return api()->success(trans('user.responses.ok'), ActiveSessionsResource::collection($agents));
    }

    /**
     * Logout a session
     * @group Public User > Session
     * @param TerminateSessionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function signout(TerminateSessionRequest $request)
    {
        $session = $request->user()->agents()->whereId($request->get('session_id'))->first();
        if($session->token_id == $request->user()->currentAccessToken()->id)
            return api()->error('Current session can\'t be invalidated .');
        $token = $request->user()->tokens()->whereId($session->token_id);
        $session->update([
            'token_id' => null
        ]);
        $token->delete();
        return api()->success(trans('user.responses.ok'));

    }

    /**
     * Logout all other sessions
     * @group Public User > Session
     * @return \Illuminate\Http\JsonResponse
     */
    public function signOutAllOtherSessions()
    {
        $currentToken = request()->user()->currentAccessToken();
        $otherTokens = request()->user()->tokens()->where('id','<>', $currentToken->id)->pluck('id');
        if(count($otherTokens) == 0)
            return api()->error('You have only one active session');

        request()->user()->agents()->whereIn('token_id', $otherTokens)->update([
            'token_id' => null
        ]);
        request()->user()->tokens()->whereIn('id', $otherTokens)->delete();
        return api()->success(trans('user.responses.ok'));

    }
}
