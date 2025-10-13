<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Task query()
 * @mixin \Eloquent
 */
class Task extends Model
{
    use HasFactory;

    // Define the necessary fields here, if needed
    protected $fillable = ['company_name', 'product', 'start_date', 'end_date', 'status', 'assigned_to'];
}
