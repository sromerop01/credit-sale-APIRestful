<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * GET /api/users
     */
    public function index(): JsonResponse
    {
        try {
            $users = User::all();
            return ApiResponse::success(
                UserResource::collection($users),
                'Usuarios obtenidos exitosamente'
            );
        } catch (Exception $e) {
            return ApiResponse::error(
                'Error al obtener los usuarios',
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * GET /api/users/{id}
     */
    public function show(string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            return ApiResponse::success(
                new UserResource($user),
                'Usuario encontrado exitosamente'
            );

        } catch (Exception $e) {
            Log::error('Error mostrando usuario ' . $id . ': ' . $e->getMessage());
            return ApiResponse::notFound('Usuario no encontrado');
        }
    }

    /**
     * POST /api/users
     */
    public function store(StoreUserRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $user = User::create($validated);

            return ApiResponse::success(
                new UserResource($user),
                'Usuario creado exitosamente',
                Response::HTTP_CREATED
            );

        } catch (Exception $e) {
            Log::error('Error al crear usuario: '. $e->getMessage());
            return ApiResponse::error('No se puede crear el usuario. Intente nuevamente', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * PUT /api/users/{id}
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        try {
            $validated = $request->validated();

            $user = User::findOrFail($id);
            $user->update($validated);

            return ApiResponse::success(
                new UserResource($user),
                'Usuario actualizado exitosamente'
            );

        } catch (Exception $e) {
            Log::error('Error actualizando usuario: ' . $e->getMessage());
            return ApiResponse::notFound('Usuario no encontrado para actualizar');
        }
    }

    /**
     * DELETE /api/users/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (Exception $e) {
            Log::error('Error eliminando usuario: ' . $e->getMessage());
            return ApiResponse::notFound('Usuario no encontrado para eliminar');
        }
    }
}
