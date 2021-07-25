<?php

namespace App\Http\Controllers;
use App\Models\Help;

use Illuminate\Http\Request;

class HelpController extends Controller
{
    public function create (Request $request)
    {

        $data = $request->all();

        $help = new Help();
        $help->email = $data['email'];
        $help->phone = $data['phone'];
        $help->description = $data['description'];
        $help->save();

        return $help;
    }
}
