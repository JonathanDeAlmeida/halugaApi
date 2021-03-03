<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Time;

class TimeController extends Controller
{
    public function create (Request $request)
    {
        $time = new Time();
        $time->place_id = 1;
        $time->user_id = 1;
        $time->start = Carbon::now();
        $time->finish = Carbon::now()->addHour();
        $time->save();

        return $time;
    }
}
