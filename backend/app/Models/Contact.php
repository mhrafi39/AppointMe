<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contacts';
    protected $primaryKey = 'contact_id';
    const UPDATED_AT = null; // disable updated_at only

    protected $fillable = [
        'name',
        'email',
        'message',
        'created_at',
    ];
}
