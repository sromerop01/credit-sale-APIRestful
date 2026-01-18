<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreLoanRoadRequest;
use App\Http\Requests\UpdateLoanRoadRequest;
use App\Http\Resources\LoanRoadResource;
use App\Models\LoanRoad;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LoanRoadController extends Controller
{
    /**
     * GET /api/loan-roads
     * Listar todas las rutas
     */
    public function index(Request $request)
    {
        try {
            $loanRoads = LoanRoad::with(['user', 'supervisor'])
                ->when($request->get('per_page'), function($query, $perPage){

                    return $query->paginate($perPage);

                }, function($query){

                    return $query->get();
                });

                return response()->json(LoanRoadResource::collection($loanRoads), Response::HTTP_OK);

        } catch (Exception $e) {
            Log::error('Error al obtener rutas: '. $e->getMessage());

            return response()->json([
                'message' => 'Ocurrio un error inesperado al cargar las rutas',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * POST /api/loan-roads
     * Crear una nueva ruta
     */
    public function store(StoreLoanRoadRequest $request)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();
            $loanRoad = LoanRoad::create($validated);
            DB::commit();

            return new LoanRoadResource($loanRoad);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al crear ruta: '. $e->getMessage());

            return response()->json([
                'message' => 'No se puede crear la ruta. Intente nuevamente',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * GET /api/loan-roads/{id}
     * Ver una ruta específica
     */
    public function show(string $id)
    {
        try {
            $loanRoad = LoanRoad::with(['user', 'supervisor'])->findOrFail($id);
            return new LoanRoadResource($loanRoad);

        } catch (Exception $e) {
            Log::error('Error mostrando ruta ' . $id . ': ' . $e->getMessage());

            return response()->json([
                'message' => 'Error al buscar la ruta.',
                'error'   => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * PUT /api/loan-roads/{id}
     * Actualizar una ruta
     */
    public function update(UpdateLoanRoadRequest $request, string $id)
    {
        try {
            $validated = $request->validated();

            DB::beginTransaction();
            $loanRoad = LoanRoad::findOrFail($id);
            $loanRoad->update($validated);
            DB::commit();

            return new LoanRoadResource($loanRoad);

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error actualizando ruta: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error al actualizar la ruta.',
                'error'   => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * DELETE /api/loan-roads/{id}
     * Eliminar un ruta
     */
    public function destroy($id)
    {
        try {

            $loanRoad = LoanRoad::findOrFail($id);
            $loanRoad->delete();

            return response()->json([
                'message' => 'Ruta eliminada correctamente'
            ], Response::HTTP_OK);

        } catch (Exception $e) {
            Log::error('Error eliminando ruta: ' . $e->getMessage());

            return response()->json([
                'message' => 'Error al eliminar la ruta.',
                'error'   => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
