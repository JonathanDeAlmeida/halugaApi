<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Responsible;
use App\Models\Place;
use Illuminate\Support\Facades\Mail;
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

    public function deleteUser (Request $request)
    {
        $data = $request->all();

        $deleted = User::where('id', $data['user_id'])->delete();

        if ($deleted) {
            return response()->json('excluído com sucesso');
        }

    }

    public function recoverPassword (Request $request)
    {
        $data = $request->all();

        $used_email = User::where('email', $data['email'])->first();

        if (!$used_email) {
            return response()->json(['not_found' => true, 'message' => 'Email não encontrado']);
        }

        // Mail::send('email.recoverypassword', ['curso' => 'Eloquent'], function ($mail) {
        //     $mail->from('haluga.imoveis@gmail.com', 'Haluga');
        //     $mail->to('jonathan88994004@gmail.com');
        // });

        // $email = 'jonathan88994004@gmail.com';

        // $messageData = ['email' => 'jonathan88994004@gmail.com','name' => 'Jonathan'];

        // Mail::send('email.recoverypassword',$messageData,function($message) use($email){
        //     $message->from('haluga.imoveis@gmail.com', 'Haluga')->to($email)->subject('Registration with AddSpy');
        // });

        return response()->json(['not_found' => false, 'message' => 'Email enviado']);
    }
}
