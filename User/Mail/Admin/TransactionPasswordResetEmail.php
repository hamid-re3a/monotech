<?php

namespace User\Mail\Admin;

use User\Mail\SettingableMail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionPasswordResetEmail extends Mailable implements SettingableMail
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

        return $this
            ->from($setting['from'], $setting['from_name'])
            ->subject($setting['subject'])
            ->html( $setting['body']);
    }

    public function getSetting(): array
    {
        return getEmailAndTextSetting('USER_TRANSACTION_PASSWORD_HAS_BEEN_RESET_BY_ADMIN_REQUEST') ;
    }
}
