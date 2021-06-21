<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Responsible;

use Illuminate\Support\Facades\DB;
use Validator;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function create (Request $request)
    {
        $data = $request->all();

        if (!$data['name'] || !$data['password'] || !$data['email']) {
            return response()->json(['user_enabled' => false, 'message' => 'Todos Os Campos Devem Ser Preenchidos']);
        }

        $used_email = User::where('email', $data['email'])->first();

        if ($used_email) {
            return response()->json(['user_enabled' => false, 'message' => 'Este Email Já Está Em Uso']);
        }

        $user = new User();
        $user->name = $data['name'];
        $user->password = bcrypt($data['password']);
        $user->email = $data['email'];
        $user->save();

        return response()->json(['user_enabled' => true, 'user' => $user]);
    }

    public function get (Request $request)
    {
        $data = $request->all();

        $user = User::where('id', $data['user_id'])->first();

        if ($user) {
            
            $responsible = Responsible::where('user_id', $user->id)->first();
            
            if ($responsible) {

                $user->place = Place::where('responsible_id', $responsible->id)->first();
            }
        }

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

        if (!$data['name'] || !$data['email']) {
            return response()->json(['user_enabled' => false, 'message' => 'Todos Os Campos Devem Ser Preenchidos']);
        }

        $used_email = User::where('email', $data['email'])->where('id', '!=', $data['id'])->first();

        if ($used_email) {
            return response()->json(['user_enabled' => false, 'message' => 'Este Email Já Está Em Uso']);
        }

        $user = User::where('id', $data['id'])->update([
            'name' => $data['name'],
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

        if (!$data['email'] || !$data['password']) {
            return response()->json(['user_enabled' => false, 'message' => 'Todos Os Campos Devem Ser Preenchidos']);
        }

        $has_user = User::where('email', $data['email'])->first();

        if (!$has_user) {
            return response()->json(['user_enabled' => false, 'message' => 'Não Há Usuário Cadastrado']);
        }

        if (crypt($data['password'], $has_user->password) != $has_user->password) {
            return response()->json(['user_enabled' => false, 'message' => 'Senha Incorreta']);
        }

        return response()->json(['user_enabled' => true, 'user_id' => $has_user->id]);
    }
}
