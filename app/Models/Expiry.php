<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expiry extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function product()
    {
        return $this->belongsToMany(Product::class, 'expiry_entries', 'expiry_entry_no', 'product_id')->withPivot([
            'returnQuantity', 'rate', 'MRP', 'GST', 'expiry_date',
        ]);
    }

    public function dealer()
    {
        return $this->belongsTo(Dealer::class, 'dealer_id');
    }

    public function scopeWithProductAndDealer($query)
    {
        $query->with('product', 'dealer');
    }
}
