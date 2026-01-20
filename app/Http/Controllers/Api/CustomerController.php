<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    /**
     * GET /api/customers
     * Puede recibir ?loan_road_id=5 para filtrar
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $customers = Customer::with('loanRoad')
                ->orderBy('order', 'asc')
                ->paginate($perPage);

            return ApiResponse::paginated(
                $customers,
                CustomerResource::class,
                'Clientes obtenidos exitosamente'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Error al obtener los clientes',
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }


    /**
     * GET /api/customers/{id}
     */
    public function show(string $id): JsonResponse
    {
        try {
            $customer = Customer::with('loanRoad')->findOrFail($id);
            return ApiResponse::success(
                new CustomerResource($customer),
                'Cliente encontrado exitosamente'
            );

        } catch (Exception $e) {
            Log::error('Error mostrando cliente ' . $id . ': ' . $e->getMessage());
            return ApiResponse::notFound('Cliente no encontrado');
        }
    }


    /**
     * POST /api/customers
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $customer = Customer::create($validated);

            return ApiResponse::success(
                new CustomerResource($customer),
                'Cliente creado exitosamente',
                Response::HTTP_CREATED
            );

        } catch (Exception $e) {
            Log::error('Error al crear cliente: '. $e->getMessage());
            return ApiResponse::error('No se puede crear el cliente. Intente nuevamente', [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }


    /**
     * PUT /api/customers/{id}
     */
    public function update(UpdateCustomerRequest $request, string $id): JsonResponse
    {
        try {
            $validated = $request->validated();

            $customer = Customer::findOrFail($id);
            $customer->update($validated);

            return ApiResponse::success(
                new CustomerResource($customer),
                'Cliente actualizado exitosamente'
            );

        } catch (Exception $e) {
            Log::error('Error actualizando cliente: ' . $e->getMessage());
            return ApiResponse::notFound('Cliente no encontrado para actualizar');
        }
    }

    /**
     * DELETE /api/customers/{id}
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();

            return response()->json(null, Response::HTTP_NO_CONTENT);

        } catch (Exception $e) {
            Log::error('Error eliminando cliente: ' . $e->getMessage());
            return ApiResponse::notFound('Cliente no encontrado para eliminar');
        }
    }
}
