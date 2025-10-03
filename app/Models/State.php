<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'states';

    protected $fillable = [
        'name',
        'created_at',
        'updated_at',
    ];

    public function councils()
    {
        return $this->hasMany(Council::class);
    }
}