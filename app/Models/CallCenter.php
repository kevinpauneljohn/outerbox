<?php

namespace App\Models;

use App\ContactPerson;
use App\Lgu;
use App\Ticket;
use App\User;
use Illuminate\Database\Eloquent\Model;

class CallCenter extends Model
{

    protected $guarded = [];
    protected $table = 'call_centers';
    protected $primaryKey = 'id';

    public function users()
    {
        return $this->belongsToMany(User::class,'callcenterdetails','cc_id','user_id');
    }

    public function lgus()
    {
        return $this->hasMany(Lgu::class);
    }

    public function contactpeople()
    {
        return $this->hasManyThrough(ContactPerson::class,Lgu::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class,'call_center_id');
    }
}
