<?php

namespace App\Http\Controllers\Api;

use App\Models\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{


    public function login(Request $request)
    {

        $validated = $request->validate([
            'email' => 'required|string|email|',
            'password' => 'required|min:6'
        ]);

        if (!Auth::attempt($validated)) {

            //TODO create common trait for nify reponses
            return response(['msg' => 'Credentials not match'], 401);
        }
        $userInfo = ['user' => auth()->user(), 'token' =>   auth()->user()->createToken('API Token')->plainTextToken];
        return response(['userInfo' => $userInfo], 200);
    }

}
