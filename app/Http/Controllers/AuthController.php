<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Notifications\LoginEmailNotification;
use Illuminate\Support\Facades\Notification;
use Stevebauman\Location\Facades\Location;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
	public function register(Request $request)
	{
		try {
			$request->validate([
				'name' => 'required|string',
				'email' => 'required|string|email|unique:users',
				'password' => 'required|string|min:6',
			]);

			$user = User::create([
				'name' => $request->name,
				'email' => $request->email,
				'password' => Hash::make($request->password),
			]);

			return response()->json(['message' => 'User registered successfully'], 201);
		} catch (\Exception $e) {
			return response()->json(['message' => 'Internal server error. Error: ' . $e->getMessage()], 500);
		}
	}

	public function login(Request $request)
	{
		try {
			$request->validate([
				'email' => 'required|string|email',
				'password' => 'required|string',
			]);

			$credentials = $request->only('email', 'password');

			if (!$token = JWTAuth::attempt($credentials)) {
				return response()->json(['message' => 'Unauthorized'], 401);
			}

			// Get user location
			$location = Location::get($request->ip()); // Use your own Public IP address for testing
			// dd('id', $request->ip(), 'location', $location);

			// Send login notification in the background
			$user = Auth::user();
			$loginDetails = [
				'ip' => $request->ip(),
				'location' => $location ? $location->cityName . ', ' . $location->countryName : 'Unknown',
				'browser' => $request->header('User-Agent'),
				'date_time' => now()->toDateTimeString(),
				'name' => $user->name,
			];

			Notification::send($user, new LoginEmailNotification($loginDetails));

			return response()->json([
				'token' => $token,
				'token_type' => 'bearer',
				'expires_in' => JWTAuth::factory()->getTTL() * 60,
			]);
		} catch (\Exception $e) {
			return response()->json(['message' => 'Internal server error. Error: ' . $e->getMessage()], 500);
		}
	}

	public function ping()
	{
		return response()->json(['message' => 'Pong!']);
	}
}
