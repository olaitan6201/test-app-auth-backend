<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $data = $request->validate([
                'name'     =>  'required|string',
                'email'     =>  'required|email|unique:users,email',
                'password'  =>  'required|string'
            ]);

            $user = User::where('email', $request->email)->first();

            if ($user) return response()->json(['status' => 'error', 'message' => 'User already exist!'], 400);

            $data['password'] = Hash::make($request->password);

            if (!$user = User::create($data))
                return response()->json(['status' => 'error', 'message' => 'Unable to register!'], 400);

            return $this->onSuccessfulLogin($user);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'An error occured'], 400);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email'     =>  'required|email',
                'password'  =>  'required|string'
            ]);

            $user = User::where('email', $request->email)->first();

            if (!$user) return response()->json(['status' => 'error', 'message' => 'User does not exist!'], 400);

            if (!Hash::check($request->password, $user->password))
                return response()->json(['status' => 'error', 'message' => 'Bad credentials'], 400);

            return $this->onSuccessfulLogin($user);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => 'An error occured'], 400);
        }
    }

    public function onSuccessfulLogin($user)
    {
        $token = $user->createToken('Bearer')->plainTextToken;

        $response = [
            'status'    =>  'success',
            'message'   =>  'Login successful!',
            'data'      =>  [
                'user'              =>  $user,
                'token'             =>  $token,
                'uid'               =>  $user->id
            ]
        ];

        return response()->json($response);
    }

    public function getUser(Request $request)
    {
        $response = [
            'status'    =>  'success',
            'message'   =>  'Fetch successful!',
            'data'      =>  [
                'user'              =>  $request->user(),
                'uid'               =>  auth()->id()
            ]
        ];

        return response()->json($response);
    }

    public function logOut()
    {
        $user = auth()->user();
        if ($user) {
            $user->tokens()->delete();
            // $user->currentAccessToken()->delete();

            return response()->json([
                'status'    =>  'success',
                'message'   =>  'Logged Out'
            ], 302);
        }
        return response()->json([
            'status'    =>  'error',
            'message'   =>  'User not logged in'
        ], 400);
    }
}
