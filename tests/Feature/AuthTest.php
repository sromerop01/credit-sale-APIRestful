<?php

use App\Http\Controllers\Api\AuthController;
use App\Models\User;

// ── Login ─────────────────────────────────────────────────────────────────────

test('login con credenciales válidas no expone el token en el body y lo setea como cookie HttpOnly', function () {
    User::factory()->create(['email' => 'test@test.com', 'password' => 'password123', 'level' => 'vendedor']);

    $response = $this->postJson('/api/v1/auth/login', [
        'email'    => 'test@test.com',
        'password' => 'password123',
    ]);

    $response->assertOk()
        ->assertJsonStructure(['data' => ['token_type', 'expires_in']])
        ->assertJsonMissingPath('data.token');

    $cookie = collect($response->headers->getCookies())->first(fn ($c) => $c->getName() === AuthController::TOKEN_COOKIE);

    expect($cookie)->not->toBeNull();
    expect($cookie->isHttpOnly())->toBeTrue();
});

test('me autentica usando solo la cookie del login, sin header Authorization', function () {
    User::factory()->create(['email' => 'cookie@test.com', 'password' => 'password123', 'level' => 'vendedor']);

    $login = $this->postJson('/api/v1/auth/login', [
        'email'    => 'cookie@test.com',
        'password' => 'password123',
    ])->assertOk();

    $cookie = collect($login->headers->getCookies())->first(fn ($c) => $c->getName() === AuthController::TOKEN_COOKIE);

    // El valor de la cookie ya viene cifrado por EncryptCookies; se reenvía tal cual,
    // igual que haría el navegador, en vez de usar withCookie() (que cifraría de nuevo).
    // withCredentials() simula que el cliente incluye cookies en la petición (XHR/fetch).
    $this->withCredentials()
        ->withUnencryptedCookie(AuthController::TOKEN_COOKIE, $cookie->getValue())
        ->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('data.email', 'cookie@test.com');
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

test('logout invalida el token y limpia la cookie', function () {
    $user = User::factory()->create(['level' => 'vendedor']);
    $headers = authHeaders($user);

    $response = $this->postJson('/api/v1/auth/logout', [], $headers)->assertOk();

    $cookie = collect($response->headers->getCookies())->first(fn ($c) => $c->getName() === AuthController::TOKEN_COOKIE);
    expect($cookie)->not->toBeNull();
    expect($cookie->getExpiresTime())->toBeLessThan(time());

    // El token ya no debe ser válido
    $this->getJson('/api/v1/auth/me', $headers)->assertUnauthorized();
});
