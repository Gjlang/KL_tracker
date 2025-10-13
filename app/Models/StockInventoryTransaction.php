<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $stock_inventory_id
 * @property int|null $billboard_id
 * @property int|null $client_id
 * @property int|null $from_contractor_id
 * @property int|null $to_contractor_id
 * @property string $type
 * @property int $quantity
 * @property string $transaction_date
 * @property string|null $remarks
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Billboard|null $billboard
 * @property-read \App\Models\ClientCompany|null $client
 * @property-read \App\Models\Contractor|null $contractor
 * @property-read \App\Models\User|null $createdBy
 * @property-read \App\Models\StockInventory $stockInventory
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventoryTransaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventoryTransaction newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventoryTransaction query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventoryTransaction whereBillboardId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventoryTransaction whereClientId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventoryTransaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventoryTransaction whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventoryTransaction whereFromContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventoryTransaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventoryTransaction whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventoryTransaction whereRemarks($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventoryTransaction whereStockInventoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventoryTransaction whereToContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventoryTransaction whereTransactionDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventoryTransaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventoryTransaction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
