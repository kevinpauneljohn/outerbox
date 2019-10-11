<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class UserAgentController extends Controller
{
    /**
     * @var int $agent
     * * */
    public $agent;

    public function __construct()
    {
        $this->agent = new Agent;
    }

    /**
     * Get the client's used device
     * @return mixed
     * */
    public function check_device()
    {

        /**
         * @var string $device
         * */
        $device = "";
        if($this->agent->isDesktop() == true)
        {
            $device = "Desktop";
        }elseif ($this->agent->isMobile() == true)
        {
            $device = "Mobile";
        }elseif ($this->agent->isTablet())
        {
            $device = "Tablet";
        }

        return $device;
    }

    /**
     * display user agent
     * @return string
     * */
    public function userAgent()
    {
        $action = '<table class="table table-bordered">';
        $action .= '<tr><td><b>Ip Address:</b> '.\request()->ip().'</td><td><b>Browser:</b> '.$this->agent->browser().' '.$this->agent->version($this->agent->browser()).'</td>
                        <td><b>Device Used:</b> '.$this->check_device().'</td><td><b>Operating System: </b>'.$this->agent->platform().' '.$this->agent->version($this->agent->platform()).'</td></tr>';
        $action .= '</table>';

        return $action;
    }
}
