<?php

namespace Promotion\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use User\Models\User;

class PromotionCode extends Model
{
    use HasFactory;
    protected $fillable = [
        'code','start_date','end_date','quota','amount'
    ];


    public function assignee()
    {
        return $this->belongsToMany(User::class,'user_promotion_codes','promotion_code_id','user_id');
    }

}
