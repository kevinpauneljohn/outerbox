<?php

namespace App;

use App\Models\CallCenter;
use App\Models\Lead;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public function users()
    {
        return $this->belongsTo(User::class,'user_assigned_id');
    }

    public function leads()
    {
        return $this->belongsTo(Lead::class);
    }

    public function callcenter()
    {
        return $this->belongsTo(CallCenter::class);
    }

    public function lgus()
    {
        return $this->belongsTo(Lgu::class);
    }
}
