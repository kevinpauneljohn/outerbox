<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Http\Request;

class TestEventController extends Controller implements ShouldBroadcast
{
    //
    public $text;

    public function __construct($text)
    {
        $this->text = $text;
        error_log("CONSTRUCT");
    }

    public function broadcastOn()
    {
        error_log("broadcastOn");
        return ['test-channel'];
    }
}
