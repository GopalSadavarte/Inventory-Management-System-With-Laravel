<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;
    public function billCustomer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
    public function billProduct()
    {
        return $this->belongsToMany(Product::class, 'customer_products', 'bill_no', 'p_id')->withPivot([
            'newRate', 'newMRP', 'newQuantity', 'newDiscount',
        ]);
    }
    public function billInventory()
    {
        return $this->belongsToMany(Inventory::class, 'customer_products', 'bill_no', 'invent_id', 'id', 'inventory_id');
    }
}
