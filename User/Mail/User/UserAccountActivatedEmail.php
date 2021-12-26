<?php

namespace User\Mail\User;

use User\Mail\SettingableMail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use User\Models\UserBlockHistory;

class UserAccountActivatedEmail extends Mailable implements SettingableMail
{
    use Queueable, SerializesModels;

    public $user;
    private $user_block_history;
    private $login_attempt_count;
    private $try_in;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $user_block_history
     * @param $login_attempt_count
     * @param $try_in
     */
    public function __construct($user, $user_block_history)
    {
        $this->user = $user;
        $this->user_block_history = $user_block_history;
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
        $setting['body'] = str_replace('{{block_type}}',(is_null( $this->user_block_history->block_type) || empty( $this->user_block_history->block_type)) ? 'Unknown':  $this->user_block_history->block_type, $setting['body']);
        $setting['body'] = str_replace('{{block_reason}}',(is_null( $this->user_block_history->block_reason) || empty( $this->user_block_history->block_reason)) ? 'Unknown':  $this->user_block_history->block_reason, $setting['body']);
        if (is_null($this->user_block_history->actor))
            $actor_name = '';
        else
            $actor_name = $this->user_block_history->actor->full_name;
        $setting['body'] = str_replace('{{actor_full_name}}',(is_null( $actor_name) || empty( $actor_name)) ? 'Unknown':  $actor_name, $setting['body']);

        return $this
            ->from($setting['from'], $setting['from_name'])
            ->subject($setting['subject'])
            ->html($setting['body']);
    }


    public function getSetting(): array
    {
        if (is_null($this->user_block_history->block_type))
            $setting = getEmailAndTextSetting('USER_ACCOUNT_ACTIVATED_EMAIL');
        else
            $setting = getEmailAndTextSetting('USER_ACCOUNT_DEACTIVATED_EMAIL');

        return $setting;
    }
}
