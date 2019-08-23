<?php

namespace App\address;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'refregion';
    public $timestamps = false;

    public function provinces()
    {
        return $this->hasMany(Province::class,'regCode','regCode');
    }
}
