<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'identification' => $this->identification,
            'name' => $this->name,
            'contact' => [
                'phone' => $this->phone,
                'address' => $this->address,
            ],

            'status' => $this->delinquent ? 'Moroso' : 'Al día',
            'is_delinquent' => (bool) $this->delinquent, // Para el frontend (rojo/verde)

            'financials' => [
                'quota' => $this->quota, // Cuota diaria/semanal
                'interest' => $this->interest . '%',
                'order' => $this->order, // Orden en la lista de cobro
            ],

            // Si cargamos la relación, mostramos el nombre de la ruta, si no, null
            'route_name' => optional($this->loanRoad)->name,
            'route_id' => $this->loan_road_id,
        ];
    }
}
