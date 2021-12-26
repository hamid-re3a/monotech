<?php

namespace User\Models;

use Illuminate\Database\Eloquent\Model;

class Translate extends Model
{
    protected $fillable = [
        'key',
        'value'
    ];
}
