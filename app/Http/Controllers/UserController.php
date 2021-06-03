<?php

namespace App\Http\Controllers;
use App\Models\User;

use Illuminate\Support\Facades\DB;
use Validator;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function create (Request $request)
    {
        $data = $request->all();

        if (!$data['name'] || !$data['login'] || !$data['password'] || !$data['email']) {
            return response()->json(['user_enabled' => false, 'message' => 'Todos Os Campos Devem Ser Preenchidos']);
        }

        $used_login = User::where('login', $data['login'])->first();
        
        if ($used_login) {
            return response()->json(['user_enabled' => false, 'message' => 'Este Login Já Está Em Uso']);
        }

        $used_email = User::where('email', $data['email'])->first();

        if ($used_email) {
            return response()->json(['user_enabled' => false, 'message' => 'Este Email Já Está Em Uso']);
        }

        $user = new User();
        $user->name = $data['name'];
        $user->login = $data['login'];
        $user->password = bcrypt($data['password']);
        $user->email = $data['email'];
        $user->save();

        return response()->json(['user_enabled' => true, 'user' => $user]);
    }

    public function get (Request $request)
    {
        $data = $request->all();

        $user = User::where('id', $data['user_id'])->first();

        return response()->json($user);
    }

    public function getAll (Request $request)
    {
        $user = User::get();
        return response()->json($user);
    }

    public function edit (Request $request)
    {
        $data = $request->all();

        if (!$data['name'] || !$data['login'] || !$data['email']) {
            return response()->json(['user_enabled' => false, 'message' => 'Todos Os Campos Devem Ser Preenchidos']);
        }

        $used_login = User::where('login', $data['login'])->where('id', '!=', $data['id'])->first();
        
        if ($used_login) {
            return response()->json(['user_enabled' => false, 'message' => 'Este Login Já Está Em Uso']);
        }

        $used_email = User::where('email', $data['email'])->where('id', '!=', $data['id'])->first();

        if ($used_email) {
            return response()->json(['user_enabled' => false, 'message' => 'Este Email Já Está Em Uso']);
        }

        $user = User::where('id', $data['id'])->update([
            'name' => $data['name'],
            'login' => $data['login'],
            'email' => $data['email']
        ]);

        if (isset($data['password'])) {
            $user = User::where('id', $data['id'])->update(['password' => bcrypt($data['password'])]);
        }

        return response()->json(['user_enabled' => true, 'message' => 'Usuário Editado Com Sucesso']);
    }

    
    public function login (Request $request)
    {
        $data = $request->all();

        if (!$data['login'] || !$data['password']) {
            return response()->json(['user_enabled' => false, 'message' => 'Todos Os Campos Devem Ser Preenchidos']);
        }

        $has_user = User::where('login', $data['login'])->first();

        if (!$has_user) {
            return response()->json(['user_enabled' => false, 'message' => 'Não Há Usuário Cadastrado']);
        }

        if (crypt($data['password'], $has_user->password) != $has_user->password) {
            return response()->json(['user_enabled' => false, 'message' => 'Senha Incorreta']);
        }

        return response()->json(['user_enabled' => true, 'user_id' => $has_user->id]);
    }
}
