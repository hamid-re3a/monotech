<?php

namespace User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * User\Models\PasswordHistory
 *
 * @property int $id
 * @property string $password
 * @property int $user_id
 * @property int|null $actor_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory query()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory whereActorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordHistory whereUserId($value)
 * @mixin \Eloquent
 */
class PasswordHistory extends Model
{
    protected $guarded = [];
    use HasFactory;
}
