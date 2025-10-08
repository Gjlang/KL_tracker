<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Council extends Model
{
    use HasFactory;

    protected $fillable = [
        'state_id',
        'name',          // full name e.g. "Majlis Bandaraya Petaling Jaya"
        'abbreviation',  // short form e.g. "MBPJ"
    ];

    // 🔗 Relationships

    // Each council belongs to a state
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    // A council can have many locations
    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}
