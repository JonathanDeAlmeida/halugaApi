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
        $place->broker = $data['broker'];
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
            'broker' => $data['broker'],
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

        $place = Place::where('id', $data['id'])->first();

        return response()->json($place);
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
    
        $places = Place::select(            
            'places.id as place_id',
            'places.responsible_id',
            'places.active',
            'places.intent',
            'places.condition',
            'places.type',
            'places.area',
            'places.rooms',
            'places.bathrooms',
            'places.suites',
            'places.vacancies',
            'places.rent_value',
            'places.sale_value',
            'places.condominium_value',
            'places.iptu',
            'places.broker',
            'places.description',
            'users.name as responsible_name',
            'users.email',
            'adresses.street',
            'adresses.number',
            'adresses.district',
            'adresses.city',
            'adresses.state',
            'adresses.complement',
            'adresses.cep',
            'phones.phone'
        )
        ->leftJoin('adresses', 'adresses.place_id', '=', 'places.id')
        ->leftJoin('phones', 'phones.place_id', '=', 'places.id')
        ->leftJoin('responsibles', 'responsibles.id', '=', 'places.responsible_id')
        ->leftJoin('users', 'users.id', '=', 'responsibles.user_id')
        ->where('users.id', $data['user_id'])
        ->groupBy('places.id')
        ->orderBy('places.id', 'DESC')
        ->paginate(10);

        foreach ($places as $place) {
            $place->images = PlaceImage::where('place_id', $place->place_id)->get();
        }

        return response()->json($places);

    }

    public function deletePlace (Request $request)
    {
        $data = $request->all();

        $place = Place::where('id', $data['place_id'])->first();

        $place_images = PlaceImage::where('place_id', $data['place_id'])->get();

        foreach ($place_images as $place_image) {
            Storage::delete($place_image->name);
        }

        $deleted = Place::where('id', $data['place_id'])->delete();

        if ($deleted) {
            $deleted = Responsible::where('id', $place->responsible_id)->delete();
            return response()->json('excluído com sucesso');
        }

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

        $place->images = PlaceImage::where('place_id', $data['place_id'])->get();

        return response()->json($place);

    }

     public function postUploadFile(Request $request)
    {
        $data = $request->all();
        $file = $data['file'];

        $filename = $file->getClientOriginalName();
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        $place_image = new PlaceImage();
        $place_image->place_id = $data['place_id'];
        $place_image->save();

        $upload = $request->file->store('placeImages');
        $path = '/storage/' . $upload;

        $place_image->name = $upload;
        $place_image->path = $path;
        $place_image->save();

        $this->countImages($data['place_id']);

        return response()->json($place_image);
    }

    public function removeFile(Request $request)
    {

        $data = $request->all();

        if (isset($data['file_id'])) {

            $image = PlaceImage::where('id', $data['file_id'])->first();
        
            Storage::delete($image->name);
    
            $deleted = PlaceImage::where('id', $data['file_id'])->delete();
    
            if ($deleted) {

                $this->countImages($image->place_id);

                $place = Place::where('id', $image->place_id)->first();

                return response()->json($place);
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
            'places.id as place_id',
            'places.responsible_id',
            'places.active',
            'places.intent',
            'places.condition',
            'places.type',
            'places.area',
            'places.rooms',
            'places.bathrooms',
            'places.suites',
            'places.vacancies',
            'places.rent_value',
            'places.sale_value',
            'places.condominium_value',
            'places.iptu',
            'places.broker',
            'places.description',
            'users.name as responsible_name',
            'users.email',
            'adresses.street',
            'adresses.number',
            'adresses.district',
            'adresses.city',
            'adresses.state',
            'adresses.complement',
            'adresses.cep',
            'phones.phone'
        )
        ->leftJoin('adresses', 'adresses.place_id', '=', 'places.id')
        ->leftJoin('phones', 'phones.place_id', '=', 'places.id')
        ->leftJoin('responsibles', 'responsibles.id', '=', 'places.responsible_id')
        ->leftJoin('users', 'users.id', '=', 'responsibles.user_id')
        ->where(function($query) use ($filter) {
            // if ($filter->district) {
            //     $query->where('adresses.district', 'LIKE', "%$filter->district%");
            // }
            // if ($filter->city) {
            //     $query->where('adresses.city', 'LIKE', "%$filter->city%");
            // }
            // if ($filter->state) {
            //     $query->where('adresses.state', 'LIKE', "%$filter->state%");
            // }
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
            if ($filter->valueMin && $filter->valueMin > 0) {
                if ($filter->intent == 'rent') {
                    $query->where('places.rent_value', '>=', $filter->valueMin);
                } else {
                    $query->where('places.sale_value', '>=', $filter->valueMin);
                }
            }
            if ($filter->valueMax && $filter->valueMax > 0) {
                if ($filter->intent == 'rent') {
                    $query->where('places.rent_value', '<=', $filter->valueMax);
                } else {
                    $query->where('places.sale_value', '<=', $filter->valueMax);
                }
            }
            if ($filter->rooms) {
                $query->where('places.rooms', '>=', $filter->rooms);
            }
            if ($filter->bathrooms) {
                $query->where('places.bathrooms', '>=', $filter->bathrooms);
            }
            if ($filter->vacancies) {
                $query->where('places.vacancies', '>=', $filter->vacancies);
            }
            if ($filter->suites) {
                $query->where('places.suites', '>=', $filter->suites);
            }

            if ($filter->city) {
                $query->where('adresses.city', 'LIKE', "%$filter->city%");
            }
            if ($filter->district) {
                $query->where('adresses.district', 'LIKE', "%$filter->district%");
            }
            if ($filter->street) {
                $query->where('adresses.street', 'LIKE', "%$filter->street%");
            }

        })->groupBy('places.id')->orderBy('places.id', 'desc')->where('places.active', true)->paginate(10);

        $places_all = json_decode(json_encode($places));
        
        foreach ($places_all->data as $place) {
            $place->images = PlaceImage::where('place_id', $place->place_id)->get();
        }

        return response()->json($places_all);
    }
}
