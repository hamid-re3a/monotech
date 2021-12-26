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
 * @property int $user_id
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereUserId($value)
 * @property string|null $os
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereOs($value)
 * @property int $hit
 * @method static \Illuminate\Database\Eloquent\Builder|Ip whereHit($value)
 */
class Ip extends Model
{
    use HasFactory;
    protected $guarded = [];

//    public function agents()
//    {
//        return $this->belongsToMany(Agent::class, 'agent_ip');
//    }
}
