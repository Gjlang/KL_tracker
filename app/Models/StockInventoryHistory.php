<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property-read \App\Models\User|null $changedBy
 * @property-read \App\Models\StockInventory|null $inventory
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventoryHistory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventoryHistory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventoryHistory query()
 * @mixin \Eloquent
 */
class StockInventoryHistory extends Model
{
    use HasFactory;

    protected $table = 'stock_inventory_history';

    protected $fillable = [
        'stock_inventory_id',
        'contractor_pic',
        'client_in',
        'client_out',
        'date_in',
        'date_out',
        'remarks_in',
        'remarks_out',
        'balance_contractor',
        'balance_bgoc',
        'change_type',
        'changed_by',
        'changed_at',
    ];

    public $timestamps = false; // we use changed_at instead

    // Relationships
    public function inventory()
    {
        return $this->belongsTo(StockInventory::class, 'stock_inventory_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
