<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string|null $client_approval
 * @property string $design
 * @property string $installation
 * @property string $printing
 * @property string $company_name
 * @property string $product
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property string $status
 * @property string $section
 * @property string|null $remarks
 * @property string $site_name
 * @property int $progress
 * @property string|null $file_path
 * @property int|null $assigned_user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $client_company_id
 * @property int|null $billboard_id
 * @property-read \App\Models\User|null $assignedUser
 * @property-read \App\Models\Billboard|null $billboard
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ClientCompany> $company
 * @property-read int|null $company_count
 * @property-read mixed $file_url
 * @property-read mixed $progress_percentage
 * @property-read mixed $status_color
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job bySection($section)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job byStatus($status)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job byUser($userId)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job upcoming()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereAssignedUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereBillboardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereClientApproval($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereClientCompanyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereCompanyName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereDesign($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereInstallation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job wherePrinting($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereProduct($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereProgress($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereSection($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereSiteName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Job whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Job extends Model
{
    use HasFactory;
    // Temporarily remove SoftDeletes until we fix the database

    protected $fillable = [
        'billboard_id',
        'company_id',
        'company_name',
        'site_name',
        'product',
        'start_date',
        'end_date',
        'status',
        'section',
        'design',
        'client_approval',
        'printing',
        'installation',
        'remarks',
        'progress',
        'file_path',
        'assigned_user_id'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'progress' => 'integer'
    ];

    protected $dates = [
        'start_date',
        'end_date',
        'created_at',
        'updated_at'
    ];

    // Relationships
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function billboard()
    {
        return $this->belongsTo(Billboard::class, 'billboard_id');
    }

    public function company()
    {
        return $this->belongsToMany(ClientCompany::class);
    }

    // Accessors
    public function getStatusColorAttribute()
    {
        switch ($this->status) {
            case 'completed':
                return '#10b981';
            case 'ongoing':
                return '#f59e0b';
            case 'pending':
            default:
                return '#ef4444';
        }
    }

    public function getProgressPercentageAttribute()
    {
        return $this->progress ?? 0;
    }

    public function getFileUrlAttribute()
    {
        return $this->file_path ? asset('storage/' . $this->file_path) : null;
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeBySection($query, $section)
    {
        return $query->where('section', $section);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('assigned_user_id', $userId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
