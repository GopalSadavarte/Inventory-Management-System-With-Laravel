<?php

namespace App\Models;

use App\Models\stock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    public $timestamps = false;
    public function stock()
    {
        return $this->belongsToMany(Stock::class, 'product_stocks', 'stock_product_id', 'stock_entry_no')
            ->withPivot('purchase_rate', 'newMRP', 'newGST');
    }

    public function purchase()
    {
        return $this->belongsToMany(Purchase::class, 'purchase_entries', 'purchase_product_id', 'purchase_entry_id')
            ->withPivot('addedQuantity', 'purchase_rate', 'MRP', 'GST', 'productMFD', 'productEXP');
    }

    public function inventory()
    {
        return $this->hasMany(Inventory::class, 'product_id', 'id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_no')->select('group_id', 'group_name');
    }

    public function subGroup()
    {
        return $this->belongsTo(SubGroup::class, 'sub_group_no')->select('sub_group_id', 'sub_group_name');
    }
}
