<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfrastructureCost extends Model
{
    protected $fillable = [
        'service_name',
        'category',
        'amount',
        'month',
        'year',
        'description',
        'created_by'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
