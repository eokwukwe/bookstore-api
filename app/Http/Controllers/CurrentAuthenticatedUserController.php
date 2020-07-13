<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\JSONAPIResource;

class CurrentAuthenticatedUserController extends Controller
{
    public function show(Request $resquest)
    {
        return new JSONAPIResource($resquest->user());
    }
}
