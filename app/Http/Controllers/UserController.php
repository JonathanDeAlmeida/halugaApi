<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Responsible;
use App\Models\Place;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
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

        $authUser = $user->createToken($request->email)->plainTextToken;

        return response()->json(['user_enabled' => true, 'authUser' => $authUser, 'userId' => $user->id]);
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

        $user = User::where('email', $request->email)->first();
        
        if (!$user || !Hash::check($request->password, $user->password)) {
            // throw ValidationException::withMessages([
            //     'email' => ['The provided credentials are incorrect.'],
            // ]);
            return response()->json(['user_enabled' => false, 'message' => 'Não Autenticado']);
        }
    
        $authUser = $user->createToken($request->email)->plainTextToken;

        return response()->json(['user_enabled' => true, 'authUser' => $authUser, 'userId' => $user->id]);
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
