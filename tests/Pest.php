<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

// Genera un token JWT real para el usuario dado y devuelve los headers de autenticación.
function authHeaders(User $user): array
{
    $token = auth()->guard('api')->login($user);
    return ['Authorization' => 'Bearer ' . $token];
}
