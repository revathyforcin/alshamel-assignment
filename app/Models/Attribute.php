<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    public function options()
    {
        return $this->hasMany(AttributeOption::class);
    }
}
