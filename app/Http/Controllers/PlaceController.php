<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Place;
use App\Models\Responsible;
use App\Models\Adresse;
use App\Models\Phone;
use App\Models\Time;
use App\Models\PlaceImage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    public function create (Request $request)
    {

        $data = $request->all();
        
        $responsible = new Responsible();
        $responsible->user_id = $data['userId'];
        $responsible->save();

        $place = new Place();
        $place->responsible_id = $responsible->id;
        $place->intent = $data['intent'];
        $place->condition = $data['condition'];
        $place->type = $data['type'];
        $place->area = $data['area'];
        $place->rooms = $data['rooms'];
        $place->bathrooms = $data['bathrooms'];
        $place->suites = $data['suites'];
        $place->vacancies = $data['vacancies'];

        if ($data['intent'] == 'rent') {
            $place->rent_value = $data['rent_value'];
        } else {
            $place->sale_value = $data['sale_value'];
        }

        $place->condominium_value = $data['condominium_value'];
        $place->iptu = $data['iptu'];
        $place->description = $data['description'];
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

        // $this->placeImageUpload($req['file'], $place->id);

        return response()->json($place);
    }

    public function edit (Request $request)
    {

        $data = $request->all();

        Place::where('id', $data['id'])->update([
            'intent' => $data['intent'],
            'condition' => $data['condition'],
            'type' => $data['type'],
            'area' => $data['area'],
            'rooms' => $data['rooms'],
            'bathrooms' => $data['bathrooms'],
            'suites' => $data['suites'],
            'vacancies' => $data['vacancies'],
            'rent_value' => $data['rent_value'],
            'sale_value' => $data['sale_value'],
            'condominium_value' => $data['condominium_value'],
            'iptu' => $data['iptu'],
            'description' => $data['description'],
        ]);

        Adresse::where('place_id', $data['id'])->update([
            'street' => $data['street'],
            'number' => $data['number'],
            'district' => $data['district'],
            'city' => $data['city'],
            'state' => $data['state'],
            'complement' => $data['complement'],
            'cep' => $data['cep']
        ]);

        Phone::where('place_id', $data['id'])->update([
            'phone' => $data['phone'],
        ]);

        return response()->json('editado com sucesso');
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

    public function getPlaces (Request $request)
    {
        $data = $request->all();    

        $places = Place::select('users.name as responsible_name', 'places.*', 'adresses.*', 'phones.*', 'places.id as place_id')
        ->leftJoin('adresses', 'adresses.place_id', '=', 'places.id')
        ->leftJoin('phones', 'phones.place_id', '=', 'places.id')
        ->leftJoin('responsibles', 'responsibles.id', '=', 'places.responsible_id')
        ->leftJoin('users', 'users.id', '=', 'responsibles.user_id')
        ->where('users.id', $data['user_id'])
        ->get();

        foreach ($places as $place) {
            $place->images = PlaceImage::where('place_id', $place->place_id)->get();
        }

        return response()->json($places);

    }

    public function deletePlace (Request $request)
    {
        $data = $request->all();

        $place = Place::where('id', $data['place_id'])->first();

        $deleted = Place::where('id', $data['place_id'])->delete();

        if ($deleted) {
            $deleted = Responsible::where('id', $place->responsible_id)->delete();
            return response()->json('excluído com sucesso');
        }

    }

    public function getPlace (Request $request)
    {
        $data = $request->all();
        // $place_id = isset($data['place_id']) ? $data['place_id'] : null;

        // if (isset($data['user_id'])) {
        //     $place_user = User::select('places.id')
        //     ->leftJoin('responsibles', 'responsibles.user_id', '=', 'users.id')
        //     ->leftJoin('places', 'places.responsible_id', '=', 'responsibles.id')
        //     ->where('users.id', $data['user_id'])
        //     ->first();

        //     $place_id = $place_user->id;
        // }

        $place = Place::select('users.name as responsible_name', 'places.*', 'adresses.*', 'phones.*')
        ->leftJoin('adresses', 'adresses.place_id', '=', 'places.id')
        ->leftJoin('phones', 'phones.place_id', '=', 'places.id')
        ->leftJoin('responsibles', 'responsibles.id', '=', 'places.responsible_id')
        ->leftJoin('users', 'users.id', '=', 'responsibles.user_id')
        ->where('places.id', $data['place_id'])
        ->first();

        $place->images = PlaceImage::where('place_id', $data['place_id'])->get();

        return response()->json($place);

    }

     public function postUploadFile(Request $request)
    {
        
        $req = $request->all();
        $resp = $this->placeImageUpload($req['file'], $req['place_id']);

        return response()->json($resp);
    }

    public function placeImageUpload ($file, $place_id)
    {

        $filename = $file->getClientOriginalName();
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        $place_image = new PlaceImage();
        $place_image->place_id = $place_id;
        $place_image->save();
        
        $nameDate = Carbon::now()->format('YmdHms') . 'i' . $place_image->id . 'p' . $place_image->place_id;
        $name = $nameDate . '.' . $extension;

        $file->storeAs('public/placeImages', $name);
        $path = '/storage/placeImages/' . $name;

        $place_image->name = $name;
        $place_image->path = $path;
        $place_image->save();

        $this->countImages($place_id);

        return $place_image;
    }

    public function removeFile(Request $request)
    {

        $data = $request->all();

        if (isset($data['file_id'])) {

            $image = PlaceImage::where('id', $data['file_id'])->first();
        
            Storage::delete('public/placeImages/' . $image->name); 
    
            $deleted = PlaceImage::where('id', $data['file_id'])->delete();
    
            if ($deleted) {

                $this->countImages($image->place_id);

                return response()->json('excluído com sucesso');
            }
        }   
        return response()->json('imagem não encontrada');

    }

    public function countImages ($place_id) {
        
        $count_images = PlaceImage::where('place_id', $place_id)->count();
        
        if ($count_images > 4) {
            Place::where('id', $place_id)->update(['active' => true]);
        } else {
            Place::where('id', $place_id)->update(['active' => false]);
        }
    }

    public function getPlaceImages (Request $request)
    {
        $data = $request->all();
        
        $place_images = PlaceImage::where('place_id', $data['place_id'])->get();

        return response()->json($place_images);
    }

    public function getFilterPlace (Request $request)
    {
        $data = json_encode($request->all());
        $filter = json_decode($data);
        
        $places = Place::select(
            'users.name as responsible_name',
            'places.*',
            'adresses.*',
            'phones.*'
        )
        ->leftJoin('adresses', 'adresses.place_id', '=', 'places.id')
        ->leftJoin('phones', 'phones.place_id', '=', 'places.id')
        ->leftJoin('responsibles', 'responsibles.id', '=', 'places.responsible_id')
        ->leftJoin('users', 'users.id', '=', 'responsibles.user_id')
        ->where(function($query) use ($filter) {
            if ($filter->district) {
                $query->where('adresses.district', 'LIKE', "%$filter->district%");
            }
            if ($filter->city) {
                $query->where('adresses.city', 'LIKE', "%$filter->city%");
            }
            if ($filter->state) {
                $query->where('adresses.state', 'LIKE', "%$filter->state%");
            }   
            if ($filter->intent) {
                $query->where('places.intent', 'LIKE', "%$filter->intent%");
            }
            if ($filter->condition) {
                $query->where('places.condition', 'LIKE', "%$filter->condition%");
            }
            if ($filter->type) {
                $query->where('places.type', 'LIKE', "%$filter->type%");
            }
            if ($filter->areaMin) {
                $query->where('places.area', '>=', $filter->areaMin);
            }
            if ($filter->areaMax) {
                $query->where('places.area', '<=', $filter->areaMax);
            }
            if ($filter->rentValueMin) {
                $query->where('places.rent_value', '>=', $filter->rentValueMin);
            }
            if ($filter->rentValueMax) {
                $query->where('places.rent_value', '<=', $filter->rentValueMax);
            }
            if ($filter->saleValueMin) {
                $query->where('places.sale_value', '>=', $filter->saleValueMin);
            }
            if ($filter->saleValueMax) {
                $query->where('places.sale_value', '<=', $filter->saleValueMax);
            }
            if ($filter->rooms) {
                $query->where('places.rooms', '=', $filter->rooms);
            }
            if ($filter->bathrooms) {
                $query->where('places.bathrooms', '=', $filter->bathrooms);
            }
            if ($filter->vacancies) {
                $query->where('places.vacancies', '=', $filter->vacancies);
            }
            if ($filter->suites) {
                $query->where('places.suites', '=', $filter->suites);
            }
        })->where('places.active', true)->get();

        foreach ($places as $place) {
            $place->images = PlaceImage::where('place_id', $place->place_id)->get();
        }

        return response()->json($places);
    }
}
