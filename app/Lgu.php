<?php

namespace App;

use App\Models\CallCenter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lgu extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    protected $table = 'lgus';

    public function callcenter()
    {
        return $this->belongsTo(CallCenter::class);
    }

    public function contactpeople()
    {
        return $this->hasMany(ContactPerson::class,'lgu_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class,'lgu_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class,'contact_people','lgu_id','user_id');
    }
}

