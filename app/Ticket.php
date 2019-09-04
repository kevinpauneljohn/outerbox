<?php

namespace App;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public function users()
    {
        return $this->belongsTo(User::class);
    }

    public function leads()
    {
        return $this->belongsTo(Lead::class);
    }
}
