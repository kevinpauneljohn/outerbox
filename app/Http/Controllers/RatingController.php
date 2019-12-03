<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rating;

class RatingController extends Controller
{
    /**
     * Add rating after a request
     * @param request
     * @author Jovito Pangan
     * Created December 03, 2019
     */
    public function addRating(Request $request)
    {
        $rating = new Rating;
        $rating->ticket_id = $request->ticket_id;
        $rating->user_id = $request->user_id;
        $rating->lgu_id = $request->lgu_id;
        $rating->stars = $request->stars;
        $rating->notes = $request->notes;

        $message = ($rating->save()) ? ['success' => true] : ['success' =>false];

        return response()->json($message);
    }
}
