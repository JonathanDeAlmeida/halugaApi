<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Adresse;

class AddressController extends Controller
{
    public function create ()
    {
        $address = new Adresse();
        $address->place_id = 1;
        $address->street = '1050';
        $address->number = 250;
        $address->district = 'Queens';
        $address->city = 'New York';
        $address->state = 'New York';
        $address->complement = 'Central Park Side';
        $address->cep = '89615-632';
        $address->save();

        return $address;
    }
}
