<?php

namespace Promotion\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromotionCode extends Model
{
    use HasFactory;
    protected $fillable = [
        'code','start_date','end_date','quota','amount'
    ];

}
