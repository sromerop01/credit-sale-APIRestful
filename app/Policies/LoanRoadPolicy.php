<?php

namespace App\Policies;

use App\Models\LoanRoad;
use App\Models\User;

class LoanRoadPolicy
{
    public function view(User $user, LoanRoad $loanRoad): bool
    {
        return match ($user->level) {
            'administrador', 'supervisor' => true,
            'vendedor'                    => $loanRoad->user_id === $user->id,
            default                       => false,
        };
    }

    public function update(User $user, LoanRoad $loanRoad): bool
    {
        return match ($user->level) {
            'administrador' => true,
            'supervisor'    => $loanRoad->supervisor_id === $user->id,
            default         => false,
        };
    }
}
