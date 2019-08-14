<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CallCenter extends Model
{
    public function staffs()
    {
        return $this->belongsToMany('App\User','callcenterdetails','user_id','cc_id');
    }
}
