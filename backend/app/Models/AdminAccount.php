<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AdminAccount extends Authenticatable
{
    use Notifiable;

    // Define the table
    protected $table = 'admin_accounts';

    // Define primary key if not 'id'
    protected $primaryKey = 'id'; // change if your column name is different

    // Fillable fields
    protected $fillable = [
        'name',
        'email',
        'password',
        // add other columns from your admin_accounts table
    ];

    // Hide sensitive data
    protected $hidden = [
        'password',
        'remember_token',
    ];
}
