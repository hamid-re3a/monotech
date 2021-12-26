<?php

namespace User\Http\Controllers\Front;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Laravel\Sanctum\PersonalAccessToken;
use User\Http\Requests\Auth\OtpRequest;

class LoginSecurityController extends Controller
{

    /**
     * Generate 2FA secret key
     * @group
     * Auth
     */
    public function generate2faSecret()
    {
        $user = auth()->user();

        $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());

        if ($user->google2fa_enable) {
            return api()->success('user.responses.2FA-is-already-enabled');
        }

        if (is_null($user->google2fa_secret)) {
            $user->google2fa_enable = false;
            $user->google2fa_secret = $google2fa->generateSecretKey();
            $user->save();
        }

        $google2fa_url = $google2fa->getQRCodeInline(
            getSetting("APP_NAME"),
            $user->email,
            $user->google2fa_secret
        );
        $secret_key = $user->google2fa_secret;

        $data = array(
            'secret' => $secret_key,
            'google2fa_url' => $google2fa_url
        );

        return api()->success(trans('user.responses.success'), $data);

    }

    /**
     * Add 2fa on token
     * @group
     * Auth
     */
    public function add2faOnToken(OtpRequest $request)
    {
        $user = auth()->user();
        $this->restrictUserAbilities($user);
        return api()->success();
    }

    /**
     * Enable 2FA
     * @group
     * Auth
     */
    public function enable2fa(OtpRequest $request)
    {
        $user = auth()->user();


        $google2fa = (new \PragmaRX\Google2FAQRCode\Google2FA());

        $secret = $request->input('one_time_password');
        $valid = $google2fa->verifyKey($user->google2fa_secret, $secret);

        if ($valid) {
            $this->restrictUserAbilities($user);

            $user->google2fa_enable = true;
            $user->save();
            return api()->success(trans('user.responses.2FA-is-enabled-successfully'));
        } else {
            $message = trans('user.responses.Invalid-verification-Code-Please-try-again');
            $errors = [
                'one_time_password' => $message
            ];
            return api()->error($message, '', 422, $errors);
        }
    }

    /**
     * Disable 2FA
     * @group
     * Auth
     */
    public function disable2fa(OtpRequest $request)
    {
        $user = auth()->user();
        $user->google2fa_enable = false;
        $user->save();
        return api()->success(trans('user.responses.2FA-is-now-disabled'));
    }

    /**
     * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
     */
    private function restrictUserAbilities(?\Illuminate\Contracts\Auth\Authenticatable $user): void
    {
        /** @var  $access_token  PersonalAccessToken */
        $access_token = $user->currentAccessToken();
        $access_token->update([
            'abilities' => array_merge($access_token->abilities, ['hasPassed:2fa'])
        ]);
    }
}
