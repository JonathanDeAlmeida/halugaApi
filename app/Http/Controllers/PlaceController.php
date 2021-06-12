<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Place;
use App\Models\Responsible;
use App\Models\Adresse;
use App\Models\Phone;
use App\Models\Time;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function create (Request $request)
    {
        // $image = $request->file('image');

        // $extension = $request->file->extension();
        // $name = "hey";
        // $nameFile = "{$name}.{$extension}";

        // $request->file->storeAs('placeImage', $nameFile);
        // $path = Storage::disk('local')->put($image, 'Contents');

        $data = $request->all();
        
        $responsible = new Responsible();
        $responsible->user_id = $data['userId'];
        $responsible->save();

        $place = new Place();
        $place->responsible_id = $responsible->id;
        $place->name = $data['name'];
        $place->description = $data['description'];
        $place->image_path = 'image path';
        $place->save();

        $address = new Adresse();
        $address->place_id = $place->id;
        $address->street = $data['street'];
        $address->number = $data['number'];
        $address->district = $data['district'];
        $address->city = $data['city'];
        $address->state = $data['state'];
        $address->complement = $data['complement'];
        $address->cep = $data['cep'];
        $address->save();

        $phone = new Phone();
        $phone->place_id = $place->id;
        $phone->phone = $data['phone'];
        $phone->save();

        return response()->json($place);
    }

    public function editPlace (Request $request)
    {
        // $user = User::where('id', $data['id'])->update([
        //     'name' => $data['name'], 
        //     'login' => $data['login'],
        //     'password' => $data['password'],
        //     'email' => $data['email']
        // ]);
    }

    public function getFilterPlace (Request $request)
    {
        $data = json_encode($request->all());
        $filter = json_decode($data);

        $places = Place::select(
            'users.name as responsible_name',
            'places.id as place_id',
            'places.name',
            'places.description',
            'places.image_path',
            'adresses.*',
            'phones.*'
        )
        ->leftJoin('adresses', 'adresses.place_id', '=', 'places.id')
        ->leftJoin('phones', 'phones.place_id', '=', 'places.id')
        ->leftJoin('responsibles', 'responsibles.id', '=', 'places.responsible_id')
        ->leftJoin('users', 'users.id', '=', 'responsibles.user_id')
        ->where(function($query) use ($filter) {
            if ($filter->name) {
                $query->where('places.name', 'LIKE', "%$filter->name%");
            }
            if ($filter->responsibleName) {
                $query->where('users.name', 'LIKE', "%$filter->responsibleName%");
            }
            if ($filter->street) {
                $query->where('adresses.street', 'LIKE', "%$filter->street%");
            }
            if ($filter->district) {
                $query->where('adresses.district', 'LIKE', "%$filter->district%");
            }
            if ($filter->city) {
                $query->where('adresses.city', 'LIKE', "%$filter->city%");
            }
            if ($filter->cep) {
                $query->where('adresses.cep', 'LIKE', "%$filter->cep%");
            }
            if ($filter->state) {
                $query->where('adresses.state', 'LIKE', "%$filter->state%");
            }
            if ($filter->number) {
                $query->where('adresses.number', 'LIKE', "%$filter->number%");
            }    
        })->get();

        return response()->json($places);
    }

    public function getPlaceTimes (Request $request)
    {
        $data = $request->all();

        $times = Time::where('place_id', $data['place_id'])->where('selected_date', $data['selectedDate'])
        ->orderBy('start')
        ->orderBy('finish')
        ->get();

        foreach ($times as $time) {
            $time->start = Carbon::parse($time->start)->format('H:i');
            $time->finish = Carbon::parse($time->finish)->format('H:i');
        }

        return response()->json($times);
    }

    public function getPlace (Request $request)
    {
        $data = $request->all();

        $place = Place::select('users.name as responsible_name', 'places.*', 'adresses.*', 'phones.*')
        ->leftJoin('adresses', 'adresses.place_id', '=', 'places.id')
        ->leftJoin('phones', 'phones.place_id', '=', 'places.id')
        ->leftJoin('responsibles', 'responsibles.id', '=', 'places.responsible_id')
        ->leftJoin('users', 'users.id', '=', 'responsibles.user_id')
        ->where('places.id', $data['place_id'])
        ->first();

        return response()->json($place);
    }
}
