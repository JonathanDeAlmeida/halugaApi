<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Responsible;

class ResponsibleController extends Controller
{
    public function create (Request $request)
    {
        $responsible = new Responsible();
        $responsible->user_id = 1;
        $responsible->save();

        return $responsible;
    }
}
