<?php

namespace App\Models;

use App\Ticket;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $casts = [
        'app_response'  => 'json',
    ];

    public function tickets()
    {
        return $this->hasOne(Ticket::class,'lead_id');
    }
}
