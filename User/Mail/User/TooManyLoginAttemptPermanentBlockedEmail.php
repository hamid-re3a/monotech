<?php

namespace User\Mail\User;

use User\Mail\SettingableMail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TooManyLoginAttemptPermanentBlockedEmail extends Mailable implements SettingableMail
{
    use Queueable, SerializesModels;

    public $user;
    private $login_attempt;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $login_attempt
     */
    public function __construct($user, $login_attempt)
    {
        $this->user = $user;
        $this->login_attempt = $login_attempt;
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

        $setting['body'] = str_replace('{{full_name}}',(is_null( $this->user->full_name) || empty( $this->user->full_name)) ? 'Unknown':  $this->user->full_name, $setting['body']);
        $setting['body'] = str_replace('{{country}}',(is_null( $this->login_attempt->ip->country) || empty( $this->login_attempt->ip->country)) ? 'Unknown':  $this->login_attempt->ip->country, $setting['body']);
        $setting['body'] = str_replace('{{city}}',(is_null( $this->login_attempt->ip->state_name) || empty( $this->login_attempt->ip->state_name)) ? 'Unknown':  $this->login_attempt->ip->state_name, $setting['body']);
        $setting['body'] = str_replace('{{ip}}',(is_null( $this->login_attempt->ip->ip) || empty( $this->login_attempt->ip->ip)) ? 'Unknown':  $this->login_attempt->ip->ip, $setting['body']);
        $setting['body'] = str_replace('{{browser}}',(is_null( $this->login_attempt->agent->browser) || empty( $this->login_attempt->agent->browser)) ? 'Unknown':  $this->login_attempt->agent->browser, $setting['body']);
        $setting['body'] = str_replace('{{platform}}',(is_null( $this->login_attempt->agent->platform) || empty( $this->login_attempt->agent->platform)) ? 'Unknown':  $this->login_attempt->agent->platform, $setting['body']);
        $setting['body'] = str_replace('{{status}}',(is_null( $this->login_attempt->login_status_string) || empty( $this->login_attempt->login_status_string)) ? 'Unknown':  $this->login_attempt->login_status_string, $setting['body']);

        return $this
            ->from($setting['from'], $setting['from_name'])
            ->subject($setting['subject'])
            ->html($setting['body']);
    }


    public function getSetting(): array
    {
        return getEmailAndTextSetting('TOO_MANY_LOGIN_ATTEMPTS_PERMANENT_BLOCK_EMAIL');
    }
}
