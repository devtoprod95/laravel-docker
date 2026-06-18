<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Member extends Authenticatable
{
    protected $table    = 'members';
    protected $fillable = ['username', 'password', 'name', 'role'];
    protected $hidden   = ['password'];
}