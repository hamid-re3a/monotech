<?php

namespace User\Mail\User;

use User\Mail\SettingableMail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SuccessfulEmailVerificationEmail extends Mailable implements SettingableMail
{
    use Queueable, SerializesModels;

    public $user;
    private $ip_db;
    private $agent_db;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $ip_db
     * @param $agent_db
     */
    public function __construct($user, $ip_db, $agent_db)
    {
        $this->user = $user;
        $this->ip_db = $ip_db;
        $this->agent_db = $agent_db;
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
        $setting['body'] = str_replace('{{country}}',(is_null( $this->ip_db->country) || empty( $this->ip_db->country)) ? 'Unknown':  $this->ip_db->country, $setting['body']);
        $setting['body'] = str_replace('{{city}}',(is_null( $this->ip_db->city) || empty( $this->ip_db->city)) ? 'Unknown':  $this->ip_db->city, $setting['body']);
        $setting['body'] = str_replace('{{ip}}',(is_null( $this->ip_db->ip) || empty( $this->ip_db->ip)) ? 'Unknown':  $this->ip_db->ip, $setting['body']);
        $setting['body'] = str_replace('{{browser}}',(is_null( $this->agent_db->browser) || empty( $this->agent_db->browser)) ? 'Unknown':  $this->agent_db->browser, $setting['body']);
        $setting['body'] = str_replace('{{platform}}',(is_null( $this->agent_db->platform) || empty( $this->agent_db->platform)) ? 'Unknown':  $this->agent_db->platform, $setting['body']);

        return $this
            ->from($setting['from'], $setting['from_name'])
            ->subject($setting['subject'])
            ->html($setting['body']);
    }


    public function getSetting(): array
    {
        return getEmailAndTextSetting('EMAIL_VERIFICATION_SUCCESS_EMAIL');
    }
}
