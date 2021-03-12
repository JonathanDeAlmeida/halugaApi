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

        $responsible = Responsible::where('user_id', $data['userId'])->first();
        $place = Place::where('responsible_id', $responsible->id)->first();

        $time = new Time();
        $time->place_id = $place->id;
        $time->user_id = $data['userId'];
        $time->name = $data['name'];
        $time->details = $data['details'];
        $time->selected_date = $data['selectedDate'];
        $time->start = $data['start'];
        $time->finish = $data['finish'];
        $time->save();

        return $time;
    }

    public function excluded (Request $request)
    {
        $data = $request->all();
        
        Time::where('id', $data['timeId'])->delete();
        
        return response()->json('successfully removed');
    }

    public function getTimes (Request $request)
    {
        $data = $request->all();

        $responsible = Responsible::where('user_id', $data['userId'])->first();
        $place = Place::where('responsible_id', $responsible->id)->first();

        $times = Time::where('place_id', $place->id)->where('selected_date', $data['selectedDate'])->get();

        foreach ($times as $time) {
            $time->start = Carbon::parse($time->start)->format('H:i');
            $time->finish = Carbon::parse($time->finish)->format('H:i');
        }

        return $times;
    }
}
