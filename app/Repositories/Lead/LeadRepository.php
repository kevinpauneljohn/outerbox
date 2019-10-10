<?php

namespace App\Repositories\Lead;

use App\Models\Lead;
use Carbon;
use DB;

/**
 * Class LeadRepository.
 */
class LeadRepository implements LeadRepositoryContract
{
    /**
     * @return mixed
     */
    public function completedLeadsThisMonth()
    {
        return DB::table('tickets')
            ->select(DB::raw('count(*) as total, updated_at'))
            ->where('status', 'Completed')
            ->whereBetween('updated_at', [Carbon::now()->startOfMonth(), Carbon::now()])
            ->groupBy('updated_at')
            ->get();
    }

    /**
     * @return mixed
     */
    public function completedLeadsMonthly()
    {
        // return DB::table('tickets')
        //     ->select(DB::raw('count(*) as month, created_at'))
        //     ->groupBy(DB::raw('YEAR(created_at), MONTH(created_at)'))
        //     ->get();
    }

    /**
     * @return mixed
     */
    public function createdLeadsMonthly()
    {
        return DB::table('tickets')
            ->select(DB::raw('count(*) as month, updated_at'))
            ->where('status', 'Completed')
            ->groupBy('updated_at')
            ->get();
    }
}

?>
