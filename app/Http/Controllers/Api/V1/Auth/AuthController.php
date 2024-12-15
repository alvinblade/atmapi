<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginRequest;
use App\Http\Requests\Api\V1\Auth\RegisterRequest;
use App\Models\User;
use App\Traits\AuthTrait;
use App\Traits\HttpResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use HttpResponses;
    use AuthTrait;

    /**
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if ($user = $this->getUser($request->username, $request->password)) {

            $token = $user->createToken('loginToken')->plainTextToken;

            return $this
                ->success(
                    data: [
                        'user' => $user,
                        'token' => $token
                    ],
                    message: "Giriş uğurla tamamlandı"
                );
        } else {
            return $this->error(message: "İstifadəçi adı və ya parol səhvdir", code: 401);
        }
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $lowerCases = $request->only('email', 'username');
        $password = ['password' => Hash::make($request->password)];
        $data = array_merge($data, $lowerCases, $password);
        $user = User::query()->create($data);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success(data: [
            'user' => $user,
            'token' => $token,
        ], message: "Qeydiyyat uğurla tamamlandı", code: 201);

    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(message: "Çıxış uğurla tamamlandı");
    }

    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'old_password' => ['required'],
            'password' => ['required', 'confirmed', 'min:8', 'max:32'],
        ]);

        $user = $request->user();

        if (Hash::check($request->input('old_password'), $user->password)) {
            $user->password = Hash::make($request->password);
            $user->update();

            return $this->success(message: "Şifrə uğurla yeniləndi");
        } else {
            return $this->error(message: "Köhnə şifrə yanlışdır", code: 400);
        }
    }
}
