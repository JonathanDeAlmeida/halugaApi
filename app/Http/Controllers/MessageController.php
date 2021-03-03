<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Message;

class MessageController extends Controller
{
    public function create (Request $request)
    {
        $message = new Message();
        $message->user_id = 1;
        $message->responsible_id = 1;
        $message->message = 'one message';
        $message->from_responsible = false;
        $message->read = false;
        $message->received = Carbon::now();
        $message->save();

        return $message;
    }
}
