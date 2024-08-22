<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseEntry extends Model
{
    use HasFactory;

    protected function productExp(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $rev = preg_split('/[\-]/', $value);
                $new = $rev[2] . '-' . $rev[1] . '-' . $rev[0];
                return $new;
            }
        );
    }

    protected function productMfd(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $rev = preg_split('/[\-]/', $value);
                $new = $rev[2] . '-' . $rev[1] . '-' . $rev[0];
                return $new;
            }
        );
    }
}
