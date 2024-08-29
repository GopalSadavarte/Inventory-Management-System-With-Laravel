<?php

namespace App\Models;

use App\Models\product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;
    public function product()
    {
        return $this->belongsToMany(Product::class, 'product_stocks', 'stock_entry_no', 'stock_product_id')
            ->withPivot('sale_rate', 'purchase_rate', 'MRP', 'GST', 'addedQuantity');
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class);
    }

    public function scopeWithProductAndDealer($query)
    {
        return $query->with('product', 'dealer');
    }
}
