<?php


namespace User\Mail;

use User\Models\EmailContentSetting;

interface SettingableMail
{
    public function getSetting(): array ;
}
