<?php

namespace User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * User\Models\UserAgent
 *
 * @property int $id
 * @property string|null $ip
 * @property string|null $iso_code
 * @property string|null $country
 * @property string|null $city
 * @property string|null $state
 * @property string|null $state_name
 * @property string|null $postal_code
 * @property string|null $lat
 * @property string|null $lon
 * @property string|null $timezone
 * @property string|null $continent
 * @property string|null $currency
 * @property string|null $default
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Ip newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ip newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Ip query()
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereCity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereContinent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereCurrency($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereDefault($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereIsoCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereLon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ip wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereState($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereStateName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereUserId($value)
 * @property string|null $os
 * @property string|null $language
 * @property string|null $device_type
 * @property string|null $platform
 * @property string|null $browser
 * @property string|null $is_desktop
 * @property string|null $is_hone
 * @property string|null $robot
 * @property string|null $is_robot
 * @property string|null $platform_version
 * @property string|null $browser_version
 * @property string|null $user_agent
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereBrowserVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereIsDesktop($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereIsHone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereIsRobot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereOs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent wherePlatformVersion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereRobot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereUserAgent($value)
 * @property string|null $is_phone
 * @property int $hit
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereHit($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Agent whereIsPhone($value)
 */
class Agent extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function ips()
    {
        return $this->belongsToMany(Ip::class,'agent_ip');
    }
}
