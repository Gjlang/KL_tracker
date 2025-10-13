<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $company_name
 * @property string $name
 * @property string $phone
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contractor newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contractor newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contractor query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contractor whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contractor whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contractor whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contractor whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contractor wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Contractor whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Contractor extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'contractors';

    protected $fillable = [
        'company_name',
        'name',
        'phone',
        'created_at',
        'updated_at'
    ];
}
