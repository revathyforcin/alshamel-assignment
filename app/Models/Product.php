<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function attributeOptions()
    {
        return $this->belongsToMany(AttributeOption::class);
    }
}
