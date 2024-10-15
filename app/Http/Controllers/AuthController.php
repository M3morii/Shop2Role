<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'username' => 'required|string|unique:users,username',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);
    
        // Membuat pengguna baru dengan role customer
        User::create([
            'username' => $validatedData['username'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role_id' => 2, // Misalnya, role_id untuk customer
        ]);
    
        return response()->json(['message' => 'User registered successfully'], 201);
    }
    


    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        // Cek user berdasarkan email
        $user = User::where('email', $request->email)->first();
    
        // Cek password
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Login failed'], 401);
        }
    
        // Generate token untuk user
        $token = $user->createToken('auth_token')->plainTextToken;
    
        // Return response dengan token
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
}}