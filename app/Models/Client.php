<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'clients';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'designation',
        'company_id',
        'created_at',
        'updated_at'
    ];

    public function company()
    {
        return $this->belongsToMany(ClientCompany::class);
    }

}
