<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @property int $id
 * @property int $contractor_id
 * @property int $balance_contractor
 * @property int $balance_bgoc
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Contractor $contractor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StockInventoryTransaction> $transactions
 * @property-read int|null $transactions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventory newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventory newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventory query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventory whereBalanceBgoc($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventory whereBalanceContractor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventory whereContractorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventory whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventory whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|StockInventory whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
