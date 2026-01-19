<?php

namespace App\Helpers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Response;

// use Illuminate\Http\Response;

class ApiResponse
{
    /**
     * Response success con datos
     */
    public static function success(
        JsonResource|array|null $data = null,
        string $message = 'Success',
        int $status = Response::HTTP_OK
    ): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data instanceof JsonResource ? $data->toArray(request()) : $data;
        }

        return response()->json($response, $status);
    }

    /**
     * Response success con paginación
     */
    public static function paginated(
        LengthAwarePaginator $paginator,
        string|null $resourceClass = null,
        string $message = 'Success',
        int $status = Response::HTTP_OK
    ): JsonResponse
    {
        $data = $paginator->items();

        if ($resourceClass) {
            // Crear colección del Resource manualmente
            $resourceCollection = $resourceClass::collection($data);
            $data = $resourceCollection->toArray(request());
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ]
        ], $status);
    }

    /**
     * Response error
     */
    public static function error(
        string $message = 'Error',
        array $errors = [],
        int $status = Response::HTTP_INTERNAL_SERVER_ERROR
    ): JsonResponse {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    /**
     * Response validation error
     */
    public static function validationError(
        string $message = 'Validation failed',
        array $errors = [],
        int $status = Response::HTTP_UNPROCESSABLE_ENTITY
    ): JsonResponse {
        return self::error($message, $errors, $status);
    }

    /**
     * Response not found
     */
    public static function notFound(
        string $message = 'Resource not found'
    ): JsonResponse {
        return self::error($message, [], Response::HTTP_NOT_FOUND);
    }

    /**
     * Response unauthorized
     */
    public static function unauthorized(
        string $message = 'Unauthorized'
    ): JsonResponse {
        return self::error($message, [], Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Response forbidden
     */
    public static function forbidden(
        string $message = 'Forbidden'
    ): JsonResponse {
        return self::error($message, [], Response::HTTP_FORBIDDEN);
    }

    /**
     * Response conflict
     */
    public static function conflict(
        string $message = 'Conflict'
    ): JsonResponse {
        return self::error($message, [], Response::HTTP_CONFLICT);
    }
}
