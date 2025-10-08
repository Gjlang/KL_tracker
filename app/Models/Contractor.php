<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
