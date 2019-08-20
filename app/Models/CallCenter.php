<?php

namespace App\Models;

use App\Lgu;
use App\User;
use Illuminate\Database\Eloquent\Model;

class CallCenter extends Model
{

    protected $guarded = [];
    protected $table = 'call_centers';
    protected $primaryKey = 'id';

    public function users()
    {
        return $this->belongsToMany(User::class,'callcenterdetails','user_id','cc_id');
    }

    public function lgus()
    {
        return $this->hasMany(Lgu::class);
    }
}
