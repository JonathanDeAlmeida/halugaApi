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

    public function get (Request $request)
    {
        $data = $request->all();

        $user = User::where('id', $data['user_id'])->first();

        return response()->json($user);
    }

    public function edit (Request $request)
    {
        $data = $request->all();

        $user = User::where('id', $data['id'])->update([
            'name' => $data['name'],
            'login' => $data['login'],
            'password' => $data['password'],
            'email' => $data['email']
        ]);

        return response()->json('ok');
    }
}
