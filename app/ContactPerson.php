<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactPerson extends Model
{
    public function lgus()
    {
        return $this->belongsTo(Lgu::class);
    }
}
