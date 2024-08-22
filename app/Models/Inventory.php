<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id')->select('id', 'product_id', 'product_name', 'sub_group_no', 'group_no');
    }
    public function scopeGetFromInventory($query)
    {
        $query->withWhereHas('product', function ($query) {
            $query->with('group', 'subGroup');
        })->orderBy('EXP')->where('current_quantity', '>', 0);
    }
}
