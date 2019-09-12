<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactPerson extends Model
{
    protected $table = 'contact_people';
    public function lgus()
    {
        return $this->belongsTo(Lgu::class,'lgu_id');
    }
}
