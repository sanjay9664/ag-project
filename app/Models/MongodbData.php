<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MongodbData extends Model
{
    protected $table = 'mongodb_data';

    protected $fillable = [
        'site_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
