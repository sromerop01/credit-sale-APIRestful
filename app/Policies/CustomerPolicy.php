<?php

namespace App\Policies;

use App\Models\Customer;
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
