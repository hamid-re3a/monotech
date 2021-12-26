<?php

namespace User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * User\Models\UserActivity
 *
 * @property int $id
// * @property int|null $user_id
 * @property int|null $ip_id
 * @property int|null $agent_id
 * @property string|null $route
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserActivity newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserActivity newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserActivity query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserActivity whereAgentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActivity whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActivity whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActivity whereIpId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActivity whereRoute($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActivity whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserActivity whereUserId($value)
 * @mixin \Eloquent
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserActivity whereDeletedAt($value)
 */
class UserActivity extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * Relations
     */
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

    public function ip()
    {
        return $this->belongsTo(Ip::class,'ip_id','id');
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class,'agent_id','id');
    }
}
