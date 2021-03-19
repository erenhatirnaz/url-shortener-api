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

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => "required|string|email",
            'password' => "required|string|min:6"
        ]);
        if ($validator->fails()) {
            return response([
                'errors' => $validator->errors()->all(),
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $user = User::firstWhere('email', $request->input('email'));
        if (! $user) {
            return response([
                'message' => "User not found!",
                'code' => Response::HTTP_NOT_FOUND,
            ], Response::HTTP_NOT_FOUND);
        }

        if (! Hash::check($request->input('password'), $user->password)) {
            return response([
                'message' => "Password mismatch!",
                'code' => Response::HTTP_UNAUTHORIZED,
            ], Response::HTTP_UNAUTHORIZED);
        }

        $token = $user->createToken('Laravel Personal Access Client')->accessToken;

        return response(['accessToken' => $token], Response::HTTP_OK);
    }

    public function show(Request $request)
    {
        return $request->user();
    }
}
