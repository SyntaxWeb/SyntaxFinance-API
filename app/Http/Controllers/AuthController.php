<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Models\User;
use Google_Client;

class AuthController extends Controller
{
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        $token = $user->createToken('web')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            return response()->json(['message' => 'Credenciais inválidas'], 401);
        }

        $user = $request->user();
        $token = $user->createToken('web')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $rules = [
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['required', 'string', 'min:6', 'confirmed'];
        }

        $data = $request->validate($rules);

        $user->fill($data);
        $user->save();

        return response()->json($user);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout efetuado']);
    }

    public function loginWithGoogle(Request $request)
    {
        $request->validate([
            'credential' => ['required', 'string'],
        ]);

        $clientId = config('services.google.client_id');
        if (! $clientId) {
            return response()->json(['message' => 'Google client id não configurado'], 500);
        }

        $client = new Google_Client(['client_id' => $clientId]);
        try {
            $payload = $client->verifyIdToken($request->string('credential'));
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Token do Google inválido'], 401);
        }

        if (! $payload || empty($payload['email'])) {
            return response()->json(['message' => 'Token do Google inválido'], 401);
        }

        $user = User::firstOrCreate(
            ['email' => $payload['email']],
            [
                'name' => $payload['name'] ?? $payload['email'],
                'password' => Str::random(32),
            ]
        );

        if ($payload['name'] ?? false) {
            $user->name = $payload['name'];
            $user->save();
        }

        $token = $user->createToken('web')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }
}
