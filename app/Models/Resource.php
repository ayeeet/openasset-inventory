<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'amount',
        'type',
        'month',
        'year',
        'created_by',
        'attachment',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
