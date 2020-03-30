<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\EmpowermentRequest;
use App\Empowerment;
use App\User;
use Illuminate\Support\Facades\Hash;
use Sentinel;
use JWTAuth;
use App\Role;

class EmpowermentController extends Controller
{
    //
    public function empower(EmpowermentRequest $request)
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

        Empowerment::create([
            "user_id" => $user->id,
            "company" => $request->get('company'),
            "dob" => $request->get('dob'),
            "gender" => $request->get('gender'),
            "city" => $request->get('city'),
            "address" => $request->get('address'),
            "state" => $request->get('state'),
            "education" => $request->get('education'),
            "interest" => $request->get('interest'),
            "country" => $request->get('country'),
        ]);

        $token = JWTAuth::fromUser($user);

        return response()->json(['status' => true, "data" => compact('user', 'token')], 201);
    }
}
