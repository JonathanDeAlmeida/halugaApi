<?php

namespace App\Http\Controllers;
use App\Models\Phone;

use Illuminate\Http\Request;

class PhoneController extends Controller
{
    public function create ()
    {
        $phone = new Phone();
        $phone->place_id = 1;
        $phone->phone = '49945454545';
        $phone->save();

        return $phone;
    }
}
