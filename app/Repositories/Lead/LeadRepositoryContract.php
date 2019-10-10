<?php

namespace App\Repositories\Lead;

interface LeadRepositoryContract
{
    public function completedLeadsThisMonth();
    //public function completedLeadsMonthly();
    public function createdLeadsMonthly();
}


?>
