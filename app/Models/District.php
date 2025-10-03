<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $table = 'districts';

    protected $fillable = [
        'state_id',
        'name',
    ];

    // 🔗 Relationships
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function locations()
    {
        return $this->hasMany(Location::class);
    }
}
