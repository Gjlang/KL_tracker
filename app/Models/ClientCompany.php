<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientCompany extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'client_companies';

    protected $fillable = [
        'name',
        'address',
        'phone',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function clients()
    {
        return $this->hasMany(Client::class, 'company_id', 'id');
    }

}
