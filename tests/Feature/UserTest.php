<?php

use App\Models\User;

// ── Index ─────────────────────────────────────────────────────────────────────

test('admin puede listar usuarios', function () {
    $admin = User::factory()->create(['level' => 'administrador']);
    User::factory()->count(3)->create();

    $this->getJson('/api/v1/users', authHeaders($admin))
        ->assertOk()
        ->assertJsonStructure(['data', 'pagination']);
});

test('supervisor no puede listar usuarios (403)', function () {
    $supervisor = User::factory()->create(['level' => 'supervisor']);

    $this->getJson('/api/v1/users', authHeaders($supervisor))->assertForbidden();
});

test('vendedor no puede listar usuarios (403)', function () {
    $vendedor = User::factory()->create(['level' => 'vendedor']);

    $this->getJson('/api/v1/users', authHeaders($vendedor))->assertForbidden();
});

// ── Show ──────────────────────────────────────────────────────────────────────

test('admin puede ver un usuario', function () {
    $admin  = User::factory()->create(['level' => 'administrador']);
    $target = User::factory()->create();

    $this->getJson("/api/v1/users/{$target->id}", authHeaders($admin))
        ->assertOk()
        ->assertJsonPath('data.email', $target->email);
});

// ── Store ─────────────────────────────────────────────────────────────────────

test('admin puede crear usuario', function () {
    $admin = User::factory()->create(['level' => 'administrador']);

    $this->postJson('/api/v1/users', [
        'name'                  => 'Nuevo',
        'email'                 => 'nuevo@test.com',
        'phone'                 => '3001234567',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
        'level'                 => 'vendedor',
    ], authHeaders($admin))->assertCreated();
});

// ── Update ────────────────────────────────────────────────────────────────────

test('admin puede actualizar usuario sin cambiar email', function () {
    $admin  = User::factory()->create(['level' => 'administrador']);
    $target = User::factory()->create(['email' => 'original@test.com']);

    // Enviar el mismo email no debe retornar 422 (verifica el fix unique->ignore)
    $this->putJson("/api/v1/users/{$target->id}", [
        'email' => 'original@test.com',
        'name'  => 'Nombre Cambiado',
    ], authHeaders($admin))->assertOk();
});

// ── Destroy ───────────────────────────────────────────────────────────────────

test('admin puede eliminar usuario (soft delete)', function () {
    $admin  = User::factory()->create(['level' => 'administrador']);
    $target = User::factory()->create();

    $this->deleteJson("/api/v1/users/{$target->id}", [], authHeaders($admin))
        ->assertNoContent();

    $this->assertSoftDeleted('users', ['id' => $target->id]);
});
