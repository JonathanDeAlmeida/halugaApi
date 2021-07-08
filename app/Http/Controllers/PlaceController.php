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

        $req = $request->all();
        $data = json_decode($req['form']);
       
        $responsible = new Responsible();
        $responsible->user_id = $data->userId;
        $responsible->save();

        $place = new Place();
        $place->responsible_id = $responsible->id;
        // $place->name = $data->name;
        $place->condition = $data->condition;
        $place->type = $data->type;
        $place->area = $data->area;
        $place->rooms = $data->rooms;
        $place->bathrooms = $data->bathrooms;
        $place->suites = $data->suites;
        $place->vacancies = $data->vacancies;
        $place->walk = $data->walk;
        $place->rentValue = $data->rentValue;
        $place->condominium = $data->condominium;
        $place->iptu = $data->iptu;
        $place->description = $data->description;
        $place->save();

        $address = new Adresse();
        $address->place_id = $place->id;
        $address->street = $data->street;
        $address->number = $data->number;
        $address->district = $data->district;
        $address->city = $data->city;
        $address->state = $data->state;
        $address->complement = $data->complement;
        $address->cep = $data->cep;
        $address->save();

        $phone = new Phone();
        $phone->place_id = $place->id;
        $phone->phone = $data->phone;
        $phone->save();

        // $this->placeImageUpload($req['file'], $place->id);

        return response()->json('save success');
    }

    public function placeImageUpload ($file, $place_id)
    {

        $filename = $file->getClientOriginalName();
        $extension = pathinfo($filename, PATHINFO_EXTENSION);

        $nameDate = Carbon::now()->format('YmdHms');
        $name = $nameDate . '.' . $extension;

        $file->storeAs('public/placeImages', $name);
        $path = '/storage/placeImages/' . $name;

        $place = Place::where('id', $place_id)->first();
        $place->image_path = $path;
        $place->save();
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
            'places.*',
            'adresses.*',
            'phones.*'
        )
        ->leftJoin('adresses', 'adresses.place_id', '=', 'places.id')
        ->leftJoin('phones', 'phones.place_id', '=', 'places.id')
        ->leftJoin('responsibles', 'responsibles.id', '=', 'places.responsible_id')
        ->leftJoin('users', 'users.id', '=', 'responsibles.user_id')
        ->where(function($query) use ($filter) {
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
                $query->where('places.rentValue', '>=', $filter->rentValueMin);
            }
            if ($filter->rentValueMax) {
                $query->where('places.rentValue', '<=', $filter->rentValueMax);
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
            if ($filter->walk) {
                $query->where('places.walk', '=', $filter->walk);
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

    public function getPlaces (Request $request)
    {
        $data = $request->all();    

        $place = Place::select('users.name as responsible_name', 'places.*', 'adresses.*', 'phones.*', 'places.id as place_id')
        ->leftJoin('adresses', 'adresses.place_id', '=', 'places.id')
        ->leftJoin('phones', 'phones.place_id', '=', 'places.id')
        ->leftJoin('responsibles', 'responsibles.id', '=', 'places.responsible_id')
        // ->leftJoin('places', 'places.responsible_id', '=', 'responsibles.id')
        ->leftJoin('users', 'users.id', '=', 'responsibles.user_id')
        ->where('users.id', $data['user_id'])
        ->get();

        return response()->json($place);

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

        return response()->json($place);

    }

     public function postUploadFile(Request $request)
    {
        return response('success', 200);
        return response()->json('oi');
        return response('success', 200);

        $file = $request->file('file');
        $info = $request->all();
        $type = $request->get('type') ? $request->get('type') : false;

        $path_to = $file->getClientMimeType();
        $ext = explode('.', $file->getClientOriginalName());
        $ext = end($ext);

        $absoluty_path = null;

        if ($type == 'video360') {

            $absoluty_path = 'storage/videos';
            $storage = Storage::disk('custom')->putFile('videos', $request->file('file'), 'public');
            $url = 'static/storage/' . $storage;
        } elseif ($ext == 'zip') {

            $storage = null;
            $destinationPath = 'public/storage/presentation';
            try {
                $file->move($destinationPath, $file->getClientOriginalName());
            } catch (Exception $exception) {
                return $exception->getMessage();
            }

            $url = str_replace('public', 'storage', $storage);
        } else {

            $absoluty_path = 'libraries/' . $path_to;
            $storage = Storage::putFile('public/libraries/' . $path_to, $request->file('file'), 'public');
            $url = str_replace('public', 'storage', $storage);
        }

        $types_with_thumb_enable = array('jpg', 'jpeg', 'png');

        $default_thumb = !in_array($file->extension(), $types_with_thumb_enable) ? 'storage/' . $file->extension() . '.png' : null;

        $file_info = array(
            'name' => str_replace(' ', '', $file->getClientOriginalName()),
            'format' => $file->extension(),
            'url' => $url,
            'path' => '/' . $path_to,
            'absoluty_path' => $absoluty_path,
            'enable' => true,
            'default_thumb' => $default_thumb
        );


        $result = $this->model->create($file_info);

        // Realizando o log da ação
        actionLog($request, $this->model, mountActionLog($result, null, 'insert'));

        if (array_key_exists('library', $info) && $info['library'] && $result->id) {
            $result->libraries()->create(array('library_id' => $info['library'], 'file_id' => $result->id));
        }

        if (array_key_exists('component_id', $info) && $info['component_id'] && $result->id) {
            $resultComp = Component::find($info['component_id']);
            $resultComp->file_id = $result->id;
            $resultComp->save();

            // Realizando o log da ação
            actionLog($request, new Component(), mountActionLog($resultComp, null, 'update'));
        }

        return response($result, 200);
    }

    public function removeFile(Request $request)
    {
        return response()->json([$request]);

        $result = $this->model->findOrFail($request->get('fileId'));

        if (!$request->get('not_delete')) {
            Storage::delete(str_replace('storage', 'public', $result->url));
        }

        if ($request->get('component_id')) {
            $resultComp = Component::find($request->get('component_id'));
            $resultComp->file_id = null;
            $resultComp->save();

            // Realizando o log da ação
            actionLog($request, new Component(), mountActionLog($resultComp, null, 'update'));
        }

        if ($request->get('libraryId')) {
            $result->libraries()->delete(array('library_id' => $request->get('libraryId'), 'file_id' => $request->get('fileId')));
        }

        if (!$request->get('not_delete')) {
            $first_result = $this->model->find($request->get('fileId'));

            $first_deleted = $result->delete();

            if ($first_deleted) {
                actionLog($request, $this->model, mountActionLog($first_result, null, 'delete'));
            }
        }

        return response()->json($result);
    }
}
