<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Authenticate user and return JWT token
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return ApiResponse::unauthorized('Usuario o contraseña inválida');
            }
        } catch (JWTException $e) {
            Log::error('JWT token generation failed', ['error' => $e->getMessage()]);

            return ApiResponse::error('No se pudo generar el token', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated user
     */
    public function me(): JsonResponse
    {
        try {
            $user = JWTAuth::user();

            $user->load(['loanRoads', 'supervisedLoanRoads'])
                ->loadCount(['loanRoads', 'supervisedLoanRoads']);

            return ApiResponse::success(
                new UserResource($user),
                'Usuario obtenido correctamente'
            );
        } catch (JWTException $e) {
            Log::error('Failed to get authenticated user', ['error' => $e->getMessage()]);

            return ApiResponse::unauthorized('Token inválido o expirado');
        }
    }

    /**
     * Log out the user (invalidate the token)
     */
    public function logout(): JsonResponse
    {
        try {
            $token = JWTAuth::getToken();

            if (! $token) {
                return ApiResponse::unauthorized('No hay token activo');
            }

            JWTAuth::invalidate($token);

            return ApiResponse::success(null, 'Sesión cerrada correctamente');
        } catch (JWTException $e) {
            Log::error('JWT logout failed', ['error' => $e->getMessage()]);

            return ApiResponse::error('No se pudo cerrar la sesión, el token no es válido', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Refresh a token
     */
    public function refreshToken(): JsonResponse
    {
        try {
            $token = JWTAuth::getToken();
            if (! $token) {
                return ApiResponse::unauthorized('No hay token para refrescar');
            }

            $newToken = JWTAuth::refresh($token);
            JWTAuth::invalidate($token);

            return $this->respondWithToken($newToken);
        } catch (JWTException $e) {
            Log::error('JWT token refresh failed', ['error' => $e->getMessage()]);

            return ApiResponse::error('Error al refrescar el token', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get the token array structure
     */
    protected function respondWithToken(string $token): JsonResponse
    {
        return ApiResponse::success([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
        ], 'Autenticación exitosa');
    }
}
