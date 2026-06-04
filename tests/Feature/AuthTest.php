<?php

use App\Models\User;

// ── Login ─────────────────────────────────────────────────────────────────────

test('login con credenciales válidas retorna token', function () {
    User::factory()->create(['email' => 'test@test.com', 'password' => 'password123', 'level' => 'vendedor']);

    $response = $this->postJson('/api/v1/auth/login', [
        'email'    => 'test@test.com',
        'password' => 'password123',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['data' => ['token', 'token_type', 'expires_in']]);
});

test('login con credenciales inválidas retorna 401', function () {
    $this->postJson('/api/v1/auth/login', ['email' => 'no@existe.com', 'password' => 'wrongpassword'])
        ->assertUnauthorized();
});

// ── Me ────────────────────────────────────────────────────────────────────────

test('me retorna el usuario autenticado', function () {
    $user = User::factory()->create(['level' => 'vendedor']);

    $this->getJson('/api/v1/auth/me', authHeaders($user))
        ->assertOk()
        ->assertJsonPath('data.email', $user->email);
});

test('me sin token retorna 401', function () {
    $this->getJson('/api/v1/auth/me')->assertUnauthorized();
});

// ── Register ──────────────────────────────────────────────────────────────────

test('admin puede registrar un usuario', function () {
    $admin = User::factory()->create(['level' => 'administrador']);

    $response = $this->postJson('/api/v1/auth/register', [
        'name'     => 'Nuevo',
        'email'    => 'nuevo@test.com',
        'phone'    => '3001234567',
        'password' => 'password123',
        'level'    => 'vendedor',
    ], authHeaders($admin));

    $response->assertCreated()->assertJsonPath('data.email', 'nuevo@test.com');
});

test('register sin token retorna 401', function () {
    $this->postJson('/api/v1/auth/register', [])->assertUnauthorized();
});

test('vendedor no puede registrar usuarios (403)', function () {
    $vendedor = User::factory()->create(['level' => 'vendedor']);

    $this->postJson('/api/v1/auth/register', [
        'name'     => 'X',
        'email'    => 'x@test.com',
        'phone'    => '3001234567',
        'password' => 'password123',
        'level'    => 'vendedor',
    ], authHeaders($vendedor))->assertForbidden();
});

// ── Logout ────────────────────────────────────────────────────────────────────

test('logout invalida el token', function () {
    $user = User::factory()->create(['level' => 'vendedor']);
    $headers = authHeaders($user);

    $this->postJson('/api/v1/auth/logout', [], $headers)->assertOk();

    // El token ya no debe ser válido
    $this->getJson('/api/v1/auth/me', $headers)->assertUnauthorized();
});
