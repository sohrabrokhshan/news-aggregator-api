<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $primaryKey = 'slug';
    protected $keyType = 'string';
    public $incrementing = false;
}
