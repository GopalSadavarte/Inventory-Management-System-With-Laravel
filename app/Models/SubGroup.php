<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubGroup extends Model
{
    use HasFactory;

    public $primaryKey = 'sub_group_id';

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_no');
    }
}
