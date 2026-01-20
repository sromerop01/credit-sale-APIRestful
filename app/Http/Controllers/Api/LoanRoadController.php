<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\ApiResponse;
use App\Http\Requests\StoreLoanRoadRequest;
use App\Http\Requests\UpdateLoanRoadRequest;
use App\Http\Resources\LoanRoadResource;
use App\Models\LoanRoad;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class LoanRoadController extends Controller
{
    /**
     * GET /api/loan-roads
     * Listar todas las rutas
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $loanRoads = LoanRoad::with(['user', 'supervisor'])
                ->paginate($perPage);

            return ApiResponse::paginated(
                $loanRoads,
                LoanRoadResource::class,
                'Rutas obtenidas exitosamente'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Error al obtener las rutas',
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * GET /api/loan-roads/{id}
     * Ver una ruta específica
     */
    public function show(string $id): JsonResponse
    {
        try {
            $loanRoad = LoanRoad::with(['user', 'supervisor'])->findOrFail($id);
            return ApiResponse::success(
                new LoanRoadResource($loanRoad),
                'Ruta encontrada exitosamente'
            );

        } catch (Exception $e) {
            Log::error('Error mostrando ruta ' . $id . ': ' . $e->getMessage());
            return ApiResponse::notFound('Ruta no encontrada');
        }
    }

    /**
     * POST /api/loan-roads
     * Crear una nueva ruta
     */
    public function store(StoreLoanRoadRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $loanRoad = LoanRoad::create($validated);

            return ApiResponse::success(
                new LoanRoadResource($loanRoad),
                'Ruta creada exitosamente',
                Response::HTTP_CREATED
            );

        } catch (Exception $e) {
            Log::error('Error al crear ruta: '. $e->getMessage());
            return ApiResponse::error('No se puede crear la ruta. Intente nuevamente', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * PUT /api/loan-roads/{id}
     * Actualizar una ruta
     */
    public function update(UpdateLoanRoadRequest $request, string $id): JsonResponse
    {
        try {
            $validated = $request->validated();

            $loanRoad = LoanRoad::findOrFail($id);
            $loanRoad->update($validated);

            return ApiResponse::success(
                new LoanRoadResource($loanRoad),
                'Ruta actualizada exitosamente'
            );

        } catch (Exception $e) {
            Log::error('Error actualizando ruta: ' . $e->getMessage());
            return ApiResponse::notFound('Ruta no encontrada para actualizar');
        }
    }

    /**
     * DELETE /api/loan-roads/{id}
     * Eliminar un ruta
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $loanRoad = LoanRoad::findOrFail($id);
            $loanRoad->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (Exception $e) {
            Log::error('Error eliminando ruta: ' . $e->getMessage());
            return ApiResponse::notFound('Ruta no encontrada para eliminar');
        }
    }
}
