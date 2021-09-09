<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserLoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Image;

class UserAuthController extends Controller
{
    public function register(Request $request)
    {
        // return response()->json([$request->avatar, $request->hasFile('avatar')]);
        $user =  User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'admin'
        ]);
        if ($request->avatar) {
            $image = $request->file('avatar');
            $path = 'avatar/' . $user->id . '/' . time() . rand(0, 100) . $image->getClientOriginalExtension();
            $make = Image::make($image->getRealPath());
            Storage::put($path, $make);
            $url = Storage::url($path);
            $user->update(['avatar' => $url]);
        }
        return response()->json([
            'success' => true,
            'message' => 'User created',
        ]);
    }
    public function login(UserLoginRequest $request)
    {
        $token = auth()->attempt($request->validated()) ?? null;
        if (!$token) return response()->json(['success' => 'false', 'message' => 'wrong credentials'], 109);
        return response()->json($this->token($token));
    }
    public function logout()
    {
        auth()->logout();
        return response()->json(['success' => true, 'message' => 'See you soon'], 200);
    }
    public function userData()
    {
        return response()->json(['user' => auth()->user()]);
    }
    public function token($token)
    {
        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'role' => auth()->user()->role
        ];
    }
}
