<?php

use User\Models\EmailContentSetting;
use User\Models\LoginAttemptSetting;
use User\Models\Setting;
use Illuminate\Support\Facades\DB;

function getSetting($key)
{

    if (DB::table('settings')->exists()) {
        $key_db = Setting::query()->where('key', $key)->first();
        if ($key_db && !empty($key_db->value))
            return $key_db->value;
    }

    if (isset(SETTINGS[$key]) && isset(SETTINGS[$key]['value']))
        return SETTINGS[$key]['value'];

    throw new Exception(trans('user.responses.main-key-settings-is-missing'));

}
if (!function_exists('getMLMGrpcClient')) {
    function getMLMGrpcClient()
    {
        return new \MLM\Services\Grpc\MLMServiceClient(env('MLM_GRPC_URL','staging-api-gateway.company.org:9598'), [
            'credentials' => \Grpc\ChannelCredentials::createInsecure()
        ]);
    }
}
if (!function_exists('getOrderGrpcClient')) {
    function getOrderGrpcClient()
    {
        return new \Orders\Services\Grpc\OrdersServiceClient(env('SUBSCRIPTION_GRPC_URL','staging-api-gateway.company.org:9596'), [
            'credentials' => \Grpc\ChannelCredentials::createInsecure()
        ]);
    }
}
function getEmailAndTextSetting($key)
{
    $email = null;

    $setting = EmailContentSetting::query()->where('key',$key)->first();
    if ($setting && !empty($setting->value)) {
        $email = $setting->toArray();
    }

    if (isset(EMAIL_CONTENT_SETTINGS[$key]))
        $email = EMAIL_CONTENT_SETTINGS[$key];

    if($email AND is_array($email)) {
        $email['from'] = env('MAIL_FROM', $email['from']);
        return $email;
    }

    throw new Exception(trans('user.responses.main-key-settings-is-missing'));
}

function getLoginAttemptSetting()
{
    $intervals = [];
    $tries = [];
    if (DB::table('login_attempt_settings')->exists() && LoginAttemptSetting::query()->get()->count() > 0) {
        $intervals_db = LoginAttemptSetting::query()->orderBy('priority', 'ASC')->get();
        foreach ($intervals_db as $ri) {
            $intervals [] = $ri->blocking_duration + $ri->duration;
            $tries [] = $ri->times;
        }
        return array($intervals, $tries);
    }


    if (isset(LOGIN_ATTEMPT_SETTINGS[0])) {
        foreach (LOGIN_ATTEMPT_SETTINGS as $ri) {
            $intervals[] = $ri['blocking_duration'] + $ri['duration'];
            $tries[] = $ri['times'];
        }
        return array($intervals, $tries);
    }

    throw new Exception(trans('user.responses.main-key-settings-is-missing'));
}


function hyphenate($str, int $every = 3)
{
    return implode("-", str_split($str, $every));
}

function sumUp(array $intervals, int $key)
{
    $all_numeric = true;
    foreach ($intervals as $sub_keys) {
        if (!(is_numeric($sub_keys))) {
            $all_numeric = false;
            break;
        }
    }
    if (!$all_numeric)
        return 0;

    if ($key == 0)
        return 0;
    return $intervals[$key - 1] + sumUp($intervals, $key - 1);
}


function secondsToHumanReadable($seconds)
{
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    $time = $dtF->diff($dtT)->format('%a days, %h hours, %i minute, %s seconds ');

    return preg_replace('/(, )?(?<!\d)0 .*?(,| )/', '', $time);
}

function getDbTranslate($key,$defaultValue = null)
{

    if(cache()->has('dbTranslates')) {
        $translate = cache()->get('dbTranslates')->where('key', $key)->first();
        if($translate)
            return $translate->value;
    }
    $translate = \User\Models\Translate::query()->firstOrCreate([
        'key' => $key
    ]);

    return $translate AND !empty($translate->value) ? $translate->value : $translate->key;

}
