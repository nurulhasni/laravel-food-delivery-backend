<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    //user register

    public function userRegister(Request $request){
       $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string',
            'phone' => 'required|string',

        ]);

        $validated = $request->all();
        $validated['password'] = Hash::make($validated['password']);
        $validated['roles'] = 'user';

        $user = User::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => $user
        ], 201);


    }

    //login
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)){
            return response()->json([
               'status' => 'failed',
               'message' => 'Invalid credentials'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login success',
            'data' => [
                'user' => $user,
                'token' => $token
            ]
        ]);


    }


    //logout
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Logout success',

        ]);

    }

    //restaurant register
    public function restaurantRegister(Request $request){

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string',
            'restaurant_name' => 'required|string',
            'restaurant_address' => 'required|string',
            'photo' => 'required|image',
            'latlong' => 'required|string',

        ]);

        $validated = $request->all();
        $validated['password'] = Hash::make($validated['password']);
        $validated['roles'] = 'restaurant';

        $user = User::create($validated);

        //check if photo is uploaded
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photo_name = time() . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('images'), $photo_name);
            $user->photo = $photo_name;
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Restaurant registered successfully',
            'data' => $user
        ], 201);


    }

    //driver register
    public function driverRegister(Request $request){

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required|string',
            'license_plate' => 'required|string',
            'photo' => 'required|image',


        ]);

        $validated = $request->all();
        $validated['password'] = Hash::make($validated['password']);
        $validated['roles'] = 'driver';

        $user = User::create($validated);

         //check if photo is uploaded
         if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photo_name = time() . '.' . $photo->getClientOriginalExtension();
            $photo->move(public_path('images'), $photo_name);
            $user->photo = $photo_name;
            $user->save();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Driver registered successfully',
            'data' => $user
        ], 201);


    }


    //update latlong user
    public function updateLatlong(Request $request) {
        $request->validate([
            'latlong' => 'required|string',

        ]);


        $user = $request->user();
        $user->latlong = $request->latlong;
        $user->save();


        return response()->json([
            'status' => 'success',
            'message' => 'Latlong updated successfully',
            'data' => $user
        ]);

    }

    // get all restaurant

    public function getRestaurant(){
        $restaurant = User::where('roles', 'restaurant')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Get all restaurant',
            'data' => $restaurant
        ]);
    }


}
