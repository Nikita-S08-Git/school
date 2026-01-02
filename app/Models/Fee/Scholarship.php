<?php

namespace App\Models\Fee;

use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    protected $table = 'scholarships';

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'max_amount',
        'is_active',
    ];

    public function applications()
    {
        return $this->hasMany(ScholarshipApplication::class);
    }
}
