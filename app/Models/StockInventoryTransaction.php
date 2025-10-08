<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StockInventoryTransaction extends Model
{
    use HasFactory;

    // Laravel automatically maps to 'stock_inventory_transactions'
    // so no need for protected $table unless you want to override.

    protected $fillable = [
        'stock_inventory_id',
        'from_contractor_id',
        'to_contractor_id',
        'billboard_id',
        'client_id',
        'type',
        'quantity',
        'transaction_date',
        'remarks',
        'created_by',
    ];

    // Relationships
    public function stockInventory()
    {
        return $this->belongsTo(StockInventory::class, 'stock_inventory_id');
    }

    public function billboard()
    {
        return $this->belongsTo(Billboard::class, 'billboard_id');
    }

    public function client()
    {
        return $this->belongsTo(ClientCompany::class, 'client_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function contractor()
    {
        return $this->hasOneThrough(
            Contractor::class,
            StockInventory::class,
            'id',                 // Foreign key on stock_inventories
            'id',                 // Foreign key on contractors
            'stock_inventory_id', // Local key on transactions
            'contractor_id'       // Local key on stock_inventories
        );
    }
}
