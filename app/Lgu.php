<?php

namespace App;

use App\Models\CallCenter;
use Illuminate\Database\Eloquent\Model;

class Lgu extends Model
{
    protected $guarded = [];
    public function callcenter()
    {
        return $this->belongsTo(CallCenter::class);
    }

    public function contactpeople()
    {
        return $this->hasMany(ContactPerson::class);
    }
}

