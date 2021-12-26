<?php

namespace User\database\seeders;

use User\Models\EmailContentSetting;
use User\Models\LoginAttemptSetting;
use User\Models\Setting;
use Illuminate\Database\Seeder;
use User\Models\LoginAttempt;

/**
 * Class AuthTableSeeder.
 */
class SettingTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (defined('SETTINGS'))
            foreach (SETTINGS as $key => $setting) {
                $key = Setting::query()->firstOrCreate([
                    'key' => $key
                ]);
                if (is_null($key->value)) {
                    $key->value = $setting['value'];
                    $key->description = $setting['description'];
                    $key->category = $setting['category'];
                    $key->save();
                }
            }

        if (defined('EMAIL_CONTENT_SETTINGS') AND is_array(EMAIL_CONTENT_SETTINGS)) {
            $now = now()->toDateTimeString();
            foreach (EMAIL_CONTENT_SETTINGS AS $key => $email) {
                if (filter_var(env('MAIL_FROM', $email['from']), FILTER_VALIDATE_EMAIL))
                    $from = env('MAIL_FROM', $email['from']);
                else
                    $from = $email['from'];
                EmailContentSetting::query()->firstOrCreate(
                    ['key' => $key],
                    [
                    'is_active' => $email['is_active'],
                    'subject' => $email['subject'],
                    'from' => $from,
                    'from_name' => $email['from_name'],
                    'body' => $email['body'],
                    'variables' => $email['variables'],
                    'variables_description' => $email['variables_description'],
                    'type' => $email['type'],
                    'created_at' => $now,
                    'updated_at' => $now
                ]);
            }
        }

        if (LoginAttemptSetting::query()->get()->count() == 0) {
            foreach (LOGIN_ATTEMPT_SETTINGS as $key => $setting) {

                LoginAttemptSetting::query()->updateOrCreate([
                    'times' => $setting['times'],
                    'duration' => $setting['duration'],
                    'priority' => $setting['priority'],
                    'blocking_duration' => $setting['blocking_duration'],
                ]);
            }
        }

    }
}
