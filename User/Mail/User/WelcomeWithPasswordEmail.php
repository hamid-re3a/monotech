<?php

namespace User\Mail\User;

use User\Mail\SettingableMail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeWithPasswordEmail extends Mailable implements SettingableMail
{
    use Queueable, SerializesModels;

    public $user;
    public $password;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $password
     */
    public function __construct($user,$password)
    {
        $this->user = $user;
        $this->password = $password;
    }


    /**
     * Build the message.
     *
     * @return $this
     * @throws \Exception
     */
    public function build()
    {
        $setting = $this->getSetting();

        $setting['body'] = str_replace('{{password}}',(is_null($this->password) || empty($this->password)) ? 'Unknown': $this->password,$setting['body']);
        $setting['body'] = str_replace('{{full_name}}',(is_null($this->user->full_name) || empty($this->user->full_name)) ? 'Unknown': $this->user->full_name,$setting['body']);
        $setting['body'] = str_replace('{{otp_expire_duration}}',(is_null(secondsToHumanReadable(getSetting("USER_EMAIL_VERIFICATION_OTP_DURATION"))) || empty(secondsToHumanReadable(getSetting("USER_EMAIL_VERIFICATION_OTP_DURATION")))) ? 'Unknown': secondsToHumanReadable(getSetting("USER_EMAIL_VERIFICATION_OTP_DURATION")),$setting['body']);

        return $this
            ->from($setting['from'], $setting['from_name'])
            ->subject($setting['subject'])
            ->html( $setting['body']);
    }

    public function getSetting(): array
    {
        return getEmailAndTextSetting('USER_REGISTRATION_WELCOME_EMAIL_WITH_PASSWORD') ;
    }
}
