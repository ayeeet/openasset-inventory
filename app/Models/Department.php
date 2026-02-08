<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name',
        'head_name',
        'location_id',
        'head_user_id',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function head()
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
