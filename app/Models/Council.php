<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $state_id
 * @property string $name
 * @property string $abbreviation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Location> $locations
 * @property-read int|null $locations_count
 * @property-read \App\Models\State|null $state
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Council newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Council newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Council query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Council whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Council whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Council whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Council whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Council whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Council whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
