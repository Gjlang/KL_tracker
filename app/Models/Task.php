<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // Define the necessary fields here, if needed
    protected $fillable = ['company_name', 'product', 'start_date', 'end_date', 'status', 'assigned_to'];
}
