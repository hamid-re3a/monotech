<?php

namespace User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * User\Models\LoginAttempt
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LoginAttempt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginAttempt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginAttempt query()
 * @method static \Illuminate\Database\Eloquent\Builder|LoginAttempt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginAttempt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginAttempt whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property int $user_id
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|LoginAttempt whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginAttempt whereUserId($value)
 * @property int|null $agent_id
 * @property int $login_status
 * @property int $is_from_new_device
 * @method static \Illuminate\Database\Eloquent\Builder|LoginAttempt whereIsFromNewDevice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginAttempt whereIsSuccess($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginAttempt whereUserAgentId($value)
 * @property int|null $ip_id
 * @property-read \User\Models\Agent|null $agent
 * @property-read \User\Models\Ip|null $ip
 * @method static \Illuminate\Database\Eloquent\Builder|LoginAttempt whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginAttempt whereIpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LoginAttempt whereLoginStatus($value)
 * @property-read string $login_status_string
 */
class LoginAttempt extends Model
{
    use HasFactory;
    protected $guarded = [];


    public function getLoginStatusStringAttribute(): string
    {
        switch ($this->login_status) {
            case LOGIN_ATTEMPT_STATUS_FAILED:
                $status = 'failed';
                break;
            case LOGIN_ATTEMPT_STATUS_SUCCESS:
                $status = 'successful';
                break;
            case LOGIN_ATTEMPT_STATUS_BLOCKED:
                $status = 'blocked';
                break;
            case LOGIN_ATTEMPT_STATUS_ON_GOING:
            default:
                $status = 'on going';
        }
        return $status;
    }

    /**
     * relations
     */

    public function ip()
    {
        return $this->belongsTo(Ip::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
