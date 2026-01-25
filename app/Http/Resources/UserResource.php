<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'level' => [
                'value' => $this->level,
                'display' => match ($this->level) {
                    'Vendedor' => 'Vendedor',
                    'Supervisor' => 'Supervisor',
                    'Administrador' => 'Administrador',
                    default => 'Desconocido'
                },
            ],

            // Contadores de relaciones
            'loan_roads_count' => $this->when(isset($this->loan_roads_count), $this->loan_roads_count),
            'supervised_loan_roads_count' => $this->when(isset($this->supervised_loan_roads_count), $this->supervised_loan_roads_count),

            // Relaciones completas
            'loan_roads' => $this->relationLoaded('loanRoads') ? LoanRoadResource::collection($this->loanRoads) : [],
            'supervised_loan_roads' => $this->relationLoaded('supervisedLoanRoads') ? LoanRoadResource::collection($this->supervisedLoanRoads) : [],

            // Metadatos
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
        ];
    }
}
