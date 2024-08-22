<?php

namespace App\Models;

use App\Models\PurchaseEntry;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    use HasFactory;

    public function stock()
    {
        return $this->hasMany(Stock::class, 'dealer_id');
    }
    public function purchase()
    {
        return $this->hasMany(Purchase::class, 'dealer_id');
    }

    public function purchaseEntry()
    {
        return $this->hasManyThrough(PurchaseEntry::class, Purchase::class, 'dealer_id', 'purchase_entry_id');
    }

    public function stockEntry()
    {
        return $this->hasManyThrough(ProductStock::class, Stock::class, 'dealer_id', 'stock_entry_no');
    }

    public function expiryEntry()
    {
        return $this->hasManyThrough(ExpiryEntry::class, Expiry::class, 'dealer_id', 'expiry_entry_no');
    }
}
