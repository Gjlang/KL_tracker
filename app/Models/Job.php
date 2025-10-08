<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

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
