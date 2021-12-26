<?php

namespace User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * User\Models\UserBlockHistory
 *
 * @property int $id
 * @property string|null $block_type
 * @property string|null $block_reason
 * @property int $user_id
 * @property int|null $actor_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|UserBlockHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserBlockHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|UserBlockHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|UserBlockHistory whereActorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBlockHistory whereBlockReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBlockHistory whereBlockType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBlockHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBlockHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBlockHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|UserBlockHistory whereUserId($value)
 * @mixin \Eloquent
 */
class UserBlockHistory extends Model
{
    protected $guarded = [];
    use HasFactory;

    /**
     * relations
     */

    public function actor()
    {
        return $this->belongsTo(User::class);
    }

    public function ip()
    {
        return $this->belongsTo(Ip::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }
}
