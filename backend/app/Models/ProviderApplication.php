<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProviderApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'real_name',
        'document_url',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
