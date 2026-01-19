<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoanRoadResource extends JsonResource
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
            'detail' => $this->detail,
            'status' => $this->inactive ? 'Inactivo' : 'Activo',
            'dates' => [
                'start' => $this->start_date?->format('Y-m-d'),
                'created' => $this->created_at?->diffForHumans(),
            ],

            'seller' => $this->user->name ?? 'Sin asignar',
            'supervisor' => $this->supervisor->name ?? 'Sin asignar',
            'metrics' => [
                'latitude' => $this->latitude,
                'commission' => $this->sales_commission . '%',
                'length' => $this->length . ' km',
            ]
        ];
    }
}
