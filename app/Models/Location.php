<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['name', 'address', 'description'];

    public function departments()
    {
        return $this->hasMany(Department::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
