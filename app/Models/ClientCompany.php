<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string|null $address
 * @property string|null $phone
 * @property int $status
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Client> $clients
 * @property-read int|null $clients_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\MasterFile> $masterFiles
 * @property-read int|null $master_files_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientCompany newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientCompany newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientCompany query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientCompany whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientCompany whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientCompany whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientCompany whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientCompany whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientCompany wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientCompany whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ClientCompany whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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

    public function masterFiles()
    {
        return $this->hasMany(MasterFile::class, 'company_id', 'id');
    }

}
