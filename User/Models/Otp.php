<?php

namespace User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * User\Models\ForgetPassword
 *
 * @property int $id
 * @property int|null $user_id
 * @property int|null $ip_id
 * @property int|null $agent_id
 * @property string $type
 * @property string|null $otp
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Otp newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Otp newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Otp query()
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereIpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereOtp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereUserId($value)
 * @mixin \Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|Otp type($type)
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Otp whereDeletedAt($value)
 */
class Otp extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function scopeType($query,$type)
    {
        return $query->whereType($type);
    }
}
