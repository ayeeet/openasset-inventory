<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'monthly_budget',
        'annual_budget',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper to check if a specific amount would exceed the annual budget
    public function wouldExceedAnnual($amount)
    {
        $currentSpent = Resource::where('year', $this->year)->sum('amount');
        return ($currentSpent + $amount) > $this->annual_budget;
    }
}
