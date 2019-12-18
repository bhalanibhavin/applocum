<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Companies extends Authenticatable
{
	use Notifiable;

    protected $guard = 'company';

    protected $fillable = [
        'name', 'email', 'password', 'logo', 'website'
    ];
}
