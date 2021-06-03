<?php

namespace App\Http\Controllers;

use App\Models\Place;
use App\Models\Responsible;
use App\Models\Adresse;
use App\Models\Phone;
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

    public function getPlace (Request $request)
    {
        $data = $request->all();

        $responsible = Responsible::where('user_id', $data['user_id'])->first();

        $place = Place::where('responsible_id', $responsible->id)->first();

        $place->address = Adresse::where('place_id', $place->id)->first();

        $place->phone = Phone::where('place_id', $place->id)->first();

        return response()->json($place);
    }
}
