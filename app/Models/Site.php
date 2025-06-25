<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class Site extends Authenticatable
{
    use Notifiable, HasRoles;

    protected $table = 'sites';
    
    protected $fillable = [
        'site_name', 
        'slug',
        'email',
        'device_id',
        'clusterID',
        'data'
    ];
}