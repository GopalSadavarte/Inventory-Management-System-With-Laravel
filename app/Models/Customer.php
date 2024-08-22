<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    public $timestamps = false;

    public function product()
    {
        return $this->hasManyThrough(CustomerProduct::class, Bill::class, 'customer_id', 'bill_no');
    }

    public function bill()
    {
        return $this->hasMany(Bill::class, 'customer_id');
    }
}
