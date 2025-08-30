<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MongodbFrontend extends Model
{
    protected $table = 'mongodb_frontend';

    protected $fillable = [
        'site_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];
}
