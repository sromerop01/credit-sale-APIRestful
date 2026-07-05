<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\LoanRoad;
use App\Models\User;

class CustomerPolicy
{
    public function view(User $user, Customer $customer): bool
    {
        return match ($user->level) {
            'administrador', 'supervisor' => true,
            'vendedor'                    => $customer->loanRoad?->user_id === $user->id,
            default                       => false,
        };
    }

    /**
     * Determina si el usuario puede crear (o reasignar) un customer en la ruta dada.
     */
    public function create(User $user, LoanRoad $loanRoad): bool
    {
        return match ($user->level) {
            'administrador' => true,
            'supervisor'    => $loanRoad->supervisor_id === $user->id,
            'vendedor'      => $loanRoad->user_id === $user->id,
            default         => false,
        };
    }

    public function update(User $user, Customer $customer): bool
    {
        return match ($user->level) {
            'administrador' => true,
            'supervisor'    => $customer->loanRoad?->supervisor_id === $user->id,
            'vendedor'      => $customer->loanRoad?->user_id === $user->id,
            default         => false,
        };
    }
}
