<?php

namespace App\Http\Controllers;

use App\User;
use App\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Sentinel;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

class ApiAuthController extends Controller
{
    public function authenticate(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$user = Sentinel::authenticate($credentials)) {
                return response()->json(['status' => false, 'error' => 'invalid credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['status' => false, 'error' => 'could not create token'], 500);
        }
        $user = User::find($user->id)->with('roles:id,name,permissions')->first();
        $token = JWTAuth::fromUser($user);
        return response()->json(compact('user', 'token', ['status' => true]));
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'phone' => $request->get('phone'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
        ]);
        $role = Role::find(1);
        $role->users()->attach($user);
        $activation = Sentinel::getActivationRepository()->create($user);
        Sentinel::getActivationRepository()->complete($user, $activation->code);

        $token = JWTAuth::fromUser($user);

        return response()->json(compact('user', 'token', ['status' => true]), 201);
    }

    public function getAuthenticatedUser()
    {
        try {

            if (!$user = JWTAuth::parseToken()->authenticate()->with('roles:id,name,permissions')->first()) {
                return response()->json(['status' => false, 'error' => 'user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['status' => false, 'error' => 'token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['status' => false, 'error' => 'token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['status' => false, 'error' => 'token_absent'], $e->getStatusCode());
        }
        return response()->json(compact('user'));
    }
    public function open()
    {
        $data = "This data is open and can be accessed without the client being authenticated";
        return response()->json(compact('data', ['status' => true]), 200);
    }

    public function closed()
    {
        $data = "Only authorized users can see this";
        return response()->json(compact('data', ['status' => true]), 200);
    }
}
