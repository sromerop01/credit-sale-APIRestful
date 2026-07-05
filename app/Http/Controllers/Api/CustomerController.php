<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\LoanRoad;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

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
            $user    = User::find(auth()->id());

            $query = Customer::with('loanRoad')->orderBy('order', 'asc');

            if ($user->level === 'vendedor') {
                $query->whereHas('loanRoad', fn ($q) => $q->where('user_id', $user->id));
            }

            if ($loanRoadId = $request->get('loan_road_id')) {
                $query->where('loan_road_id', $loanRoadId);
            }

            if ($request->has('delinquent')) {
                $query->where('delinquent', filter_var($request->get('delinquent'), FILTER_VALIDATE_BOOLEAN));
            }

            $customers = $query->paginate($perPage);

            return ApiResponse::paginated(
                $customers,
                CustomerResource::class,
                'Clientes obtenidos exitosamente'
            );
        } catch (Exception $e) {
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

            if (Gate::denies('view', $customer)) {
                return ApiResponse::forbidden('No tienes permisos para ver este cliente');
            }

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

            $loanRoad = LoanRoad::findOrFail($validated['loan_road_id']);

            if (Gate::denies('create', [Customer::class, $loanRoad])) {
                return ApiResponse::forbidden('No tienes permisos para crear clientes en esta ruta');
            }

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
            $customer = Customer::with('loanRoad')->findOrFail($id);

            if (Gate::denies('update', $customer)) {
                return ApiResponse::forbidden('No tienes permisos para editar este cliente');
            }

            $validated = $request->validated();

            if (isset($validated['loan_road_id']) && $validated['loan_road_id'] != $customer->loan_road_id) {
                $targetLoanRoad = LoanRoad::findOrFail($validated['loan_road_id']);

                if (Gate::denies('create', [Customer::class, $targetLoanRoad])) {
                    return ApiResponse::forbidden('No tienes permisos para mover este cliente a la ruta indicada');
                }
            }

            $customer->fill($validated)->save();

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
