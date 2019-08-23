<?php

namespace App\address;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'refprovince';
    public $timestamps = false;

    public function region()
    {
        return $this->belongsTo(Region::class,'regCode','regCode');
    }
}
