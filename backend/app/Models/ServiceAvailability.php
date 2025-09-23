<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceAvailability extends Model
{
    protected $table = 'services_availabilities';
    protected $primaryKey = 'availability_id';
    public $timestamps = false; // disable if you don’t have created_at / updated_at in this table

    protected $fillable = [
        'services_id',
        'date',
        'start_time',
        'end_time',
        'is_booked',
    ];

    // Relationship → each availability belongs to one service
    public function service()
    {
        return $this->belongsTo(Service::class, 'services_id', 'services_id');
    }
}
