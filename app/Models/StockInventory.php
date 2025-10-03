<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockInventory extends Model
{
    use HasFactory;

    // No need for $table because Laravel knows this is 'stock_inventories'

    protected $fillable = [
        'contractor_id',
        'balance_contractor',
        'balance_bgoc',
    ];

    // Relationships
    public function contractor()
    {
        return $this->belongsTo(Contractor::class, 'contractor_id');
    }

    public function transactions()
    {
        return $this->hasMany(StockInventoryTransaction::class, 'stock_inventory_id');
    }

    // public function history()
    // {
    //     return $this->hasMany(StockInventoryHistory::class, 'stock_inventory_id');
    // }
}
