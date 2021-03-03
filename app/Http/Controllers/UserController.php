<?php

namespace App\Http\Controllers;
use App\Models\User;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function create (Request $request)
    {
        $data = $request->all();

        $user = new User();
        $user->name = $data['name'];
        $user->login = $data['login'];
        $user->password = $data['password'];
        $user->email = $data['email'];
        $user->save();

        return response()->json($user);
    }
}
