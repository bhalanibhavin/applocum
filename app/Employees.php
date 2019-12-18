<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employees extends Model
{
    protected $fillable = [
        'full_name', 'company_id', 'email', 'phone'
    ];
}
