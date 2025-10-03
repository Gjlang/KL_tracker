<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $table = 'locations';

    protected $fillable = [
        'district_id',
        'council_id',
        'name',
        'created_by',
        'updated_by',
    ];

    // 🔗 Relationships
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function council()
    {
        return $this->belongsTo(Council::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
