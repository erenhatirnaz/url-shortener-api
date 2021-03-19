<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class ApiAuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = new User();
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->remember_token = Str::random(10);
        $user->save();

        $token = $user->createToken('Laravel Personal Access Client')->accessToken;
        return response(['accessToken' => $token], Response::HTTP_OK);
    }

    public function login()
    {
        return response([
            "message" => "Not implemented!",
            "code" => Response::HTTP_NOT_IMPLEMENTED,
        ], Response::HTTP_NOT_IMPLEMENTED);
    }

    public function show(Request $request)
    {
        return $request->user();
    }
}
