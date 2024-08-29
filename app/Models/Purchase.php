<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    public function product()
    {
        return $this->belongsToMany(Product::class, 'purchase_entries', 'purchase_entry_id', 'purchase_product_id')
            ->withPivot('addedQuantity', 'sale_rate', 'purchase_rate', 'MRP', 'GST', 'productMFD', 'productEXP');
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'dealer_id');
    }

    public function scopeWithProductAndDealer($query)
    {
        return $query->with('product', 'dealer');
    }
}
