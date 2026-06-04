<?php

use App\Models\LoanRoad;
use App\Models\User;

// ── Index ─────────────────────────────────────────────────────────────────────

test('admin puede listar todas las rutas', function () {
    $admin = User::factory()->create(['level' => 'administrador']);
    LoanRoad::factory()->count(3)->create();

    $this->getJson('/api/v1/loan-roads', authHeaders($admin))
        ->assertOk()
        ->assertJsonPath('pagination.total', 3);
});

test('supervisor puede listar todas las rutas', function () {
    $supervisor = User::factory()->create(['level' => 'supervisor']);
    LoanRoad::factory()->count(2)->create();

    $this->getJson('/api/v1/loan-roads', authHeaders($supervisor))
        ->assertOk()
        ->assertJsonPath('pagination.total', 2);
});

test('vendedor no puede listar rutas (403)', function () {
    $vendedor = User::factory()->create(['level' => 'vendedor']);

    $this->getJson('/api/v1/loan-roads', authHeaders($vendedor))->assertForbidden();
});

// ── Show ──────────────────────────────────────────────────────────────────────

test('admin puede ver cualquier ruta', function () {
    $admin    = User::factory()->create(['level' => 'administrador']);
    $loanRoad = LoanRoad::factory()->create();

    $this->getJson("/api/v1/loan-roads/{$loanRoad->id}", authHeaders($admin))->assertOk();
});

test('vendedor puede ver su propia ruta', function () {
    $vendedor = User::factory()->create(['level' => 'vendedor']);
    $loanRoad = LoanRoad::factory()->create(['user_id' => $vendedor->id]);

    $this->getJson("/api/v1/loan-roads/{$loanRoad->id}", authHeaders($vendedor))->assertOk();
});

test('vendedor no puede ver ruta ajena (403)', function () {
    $vendedor     = User::factory()->create(['level' => 'vendedor']);
    $otroVendedor = User::factory()->create(['level' => 'vendedor']);
    $loanRoad     = LoanRoad::factory()->create(['user_id' => $otroVendedor->id]);

    $this->getJson("/api/v1/loan-roads/{$loanRoad->id}", authHeaders($vendedor))->assertForbidden();
});

// ── Store ─────────────────────────────────────────────────────────────────────

test('admin puede crear una ruta', function () {
    $admin      = User::factory()->create(['level' => 'administrador']);
    $vendedor   = User::factory()->create(['level' => 'vendedor']);
    $supervisor = User::factory()->create(['level' => 'supervisor']);

    $this->postJson('/api/v1/loan-roads', [
        'name'             => 'Ruta Test',
        'start_date'       => '2026-01-01',
        'sales_commission' => 5.00,
        'length'           => 20.5,
        'latitude'         => 4.6097,
        'longitude'        => -74.0817,
        'inactive'         => false,
        'user_id'          => $vendedor->id,
        'supervisor_id'    => $supervisor->id,
    ], authHeaders($admin))->assertCreated();
});

test('supervisor no puede crear ruta (403)', function () {
    $supervisor = User::factory()->create(['level' => 'supervisor']);

    $this->postJson('/api/v1/loan-roads', [], authHeaders($supervisor))->assertForbidden();
});

// ── Update ────────────────────────────────────────────────────────────────────

test('admin puede actualizar cualquier ruta', function () {
    $admin    = User::factory()->create(['level' => 'administrador']);
    $loanRoad = LoanRoad::factory()->create();

    $this->putJson("/api/v1/loan-roads/{$loanRoad->id}", [
        'name' => 'Ruta Actualizada',
    ], authHeaders($admin))->assertOk();
});

test('supervisor puede actualizar su propia ruta', function () {
    $supervisor = User::factory()->create(['level' => 'supervisor']);
    $loanRoad   = LoanRoad::factory()->create(['supervisor_id' => $supervisor->id]);

    $this->putJson("/api/v1/loan-roads/{$loanRoad->id}", [
        'name' => 'Ruta Supervisada Actualizada',
    ], authHeaders($supervisor))->assertOk();
});

test('supervisor no puede actualizar ruta ajena (403)', function () {
    $supervisor      = User::factory()->create(['level' => 'supervisor']);
    $otroSupervisor  = User::factory()->create(['level' => 'supervisor']);
    $loanRoad        = LoanRoad::factory()->create(['supervisor_id' => $otroSupervisor->id]);

    $this->putJson("/api/v1/loan-roads/{$loanRoad->id}", [
        'name' => 'Intento',
    ], authHeaders($supervisor))->assertForbidden();
});

// ── Destroy ───────────────────────────────────────────────────────────────────

test('admin puede eliminar una ruta', function () {
    $admin    = User::factory()->create(['level' => 'administrador']);
    $loanRoad = LoanRoad::factory()->create();

    $this->deleteJson("/api/v1/loan-roads/{$loanRoad->id}", [], authHeaders($admin))
        ->assertNoContent();

    $this->assertSoftDeleted('loan_roads', ['id' => $loanRoad->id]);
});

test('supervisor no puede eliminar ruta (403)', function () {
    $supervisor = User::factory()->create(['level' => 'supervisor']);
    $loanRoad   = LoanRoad::factory()->create();

    $this->deleteJson("/api/v1/loan-roads/{$loanRoad->id}", [], authHeaders($supervisor))
        ->assertForbidden();
});
