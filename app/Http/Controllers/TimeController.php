<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Responsible;
use App\Models\Time;
use App\Models\Place;

class TimeController extends Controller
{
    public function create (Request $request)
    {
        $data = $request->all();

        // $responsible = Responsible::where('user_id', $data['userId'])->first();
        // $place = Place::where('responsible_id', $responsible->id)->first();

        // $time = new Time();
        // $time->place_id = $place->id;
        // $time->user_id = $data['userId'];
        // $time->start = Carbon::now();
        // $time->finish = Carbon::now()->addHour();
        // $time->save();

        // return $time;
        return $data;
    }
}
