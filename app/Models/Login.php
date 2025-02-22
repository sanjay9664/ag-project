<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;
use App\User;

class Login extends Model
{
    use Notifiable, HasRoles;

    protected $table = 'logins';
    protected $fillable = ['user_id', 'role', 'ip_address','status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}