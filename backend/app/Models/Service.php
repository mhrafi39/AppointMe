<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = "services";        
    protected $primaryKey = 'services_id'; 

    // Your table stores created_at/updated_at as DATE, not TIMESTAMP
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'category',
        'location',
        'price',
        'available_time',
        'created_at',
        'updated_at'
    ];

    public function profilePicture()
    {
        return $this->hasOne(ProfilePicture::class, 'user_id', 'user_id');
    }
}
