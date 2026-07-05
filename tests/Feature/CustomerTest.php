<?php

use App\Models\Customer;
use App\Models\LoanRoad;
use App\Models\User;

// ── Index (scoping por rol) ───────────────────────────────────────────────────

test('admin ve todos los customers', function () {
    $admin = User::factory()->create(['level' => 'administrador']);
    Customer::factory()->count(5)->create();

    $this->getJson('/api/v1/customers', authHeaders($admin))
        ->assertOk()
        ->assertJsonPath('pagination.total', 5);
});

test('supervisor ve todos los customers', function () {
    $supervisor = User::factory()->create(['level' => 'supervisor']);
    Customer::factory()->count(4)->create();

    $this->getJson('/api/v1/customers', authHeaders($supervisor))
        ->assertOk()
        ->assertJsonPath('pagination.total', 4);
});

test('vendedor solo ve customers de su ruta', function () {
    $vendedor = User::factory()->create(['level' => 'vendedor']);
    $otroVendedor = User::factory()->create(['level' => 'vendedor']);

    $miRuta     = LoanRoad::factory()->create(['user_id' => $vendedor->id]);
    $otraRuta   = LoanRoad::factory()->create(['user_id' => $otroVendedor->id]);

    Customer::factory()->count(3)->create(['loan_road_id' => $miRuta->id]);
    Customer::factory()->count(5)->create(['loan_road_id' => $otraRuta->id]);

    $this->getJson('/api/v1/customers', authHeaders($vendedor))
        ->assertOk()
        ->assertJsonPath('pagination.total', 3);
});

// ── Show ──────────────────────────────────────────────────────────────────────

test('admin puede ver cualquier customer', function () {
    $admin    = User::factory()->create(['level' => 'administrador']);
    $customer = Customer::factory()->create();

    $this->getJson("/api/v1/customers/{$customer->id}", authHeaders($admin))->assertOk();
});

test('vendedor puede ver customer de su ruta', function () {
    $vendedor = User::factory()->create(['level' => 'vendedor']);
    $ruta     = LoanRoad::factory()->create(['user_id' => $vendedor->id]);
    $customer = Customer::factory()->create(['loan_road_id' => $ruta->id]);

    $this->getJson("/api/v1/customers/{$customer->id}", authHeaders($vendedor))->assertOk();
});

test('vendedor no puede ver customer de otra ruta (403)', function () {
    $vendedor     = User::factory()->create(['level' => 'vendedor']);
    $otroVendedor = User::factory()->create(['level' => 'vendedor']);
    $otraRuta     = LoanRoad::factory()->create(['user_id' => $otroVendedor->id]);
    $customer     = Customer::factory()->create(['loan_road_id' => $otraRuta->id]);

    $this->getJson("/api/v1/customers/{$customer->id}", authHeaders($vendedor))->assertForbidden();
});

// ── Store ─────────────────────────────────────────────────────────────────────

test('vendedor puede crear customer en su propia ruta', function () {
    $vendedor = User::factory()->create(['level' => 'vendedor']);
    $ruta     = LoanRoad::factory()->create(['user_id' => $vendedor->id]);
    $payload  = Customer::factory()->make(['loan_road_id' => $ruta->id])->toArray();

    $this->postJson('/api/v1/customers', $payload, authHeaders($vendedor))
        ->assertCreated();
});

test('vendedor no puede crear customer en ruta ajena (403)', function () {
    $vendedor     = User::factory()->create(['level' => 'vendedor']);
    $otroVendedor = User::factory()->create(['level' => 'vendedor']);
    $otraRuta     = LoanRoad::factory()->create(['user_id' => $otroVendedor->id]);
    $payload      = Customer::factory()->make(['loan_road_id' => $otraRuta->id])->toArray();

    $this->postJson('/api/v1/customers', $payload, authHeaders($vendedor))
        ->assertForbidden();

    $this->assertDatabaseMissing('customers', ['identification' => $payload['identification']]);
});

// ── Update ────────────────────────────────────────────────────────────────────

test('vendedor puede actualizar customer de su ruta', function () {
    $vendedor = User::factory()->create(['level' => 'vendedor']);
    $ruta     = LoanRoad::factory()->create(['user_id' => $vendedor->id]);
    $customer = Customer::factory()->create(['loan_road_id' => $ruta->id]);

    $this->putJson("/api/v1/customers/{$customer->id}", [
        'name' => 'Nombre Actualizado',
    ], authHeaders($vendedor))->assertOk();
});

test('vendedor no puede actualizar customer de otra ruta (403)', function () {
    $vendedor     = User::factory()->create(['level' => 'vendedor']);
    $otroVendedor = User::factory()->create(['level' => 'vendedor']);
    $otraRuta     = LoanRoad::factory()->create(['user_id' => $otroVendedor->id]);
    $customer     = Customer::factory()->create(['loan_road_id' => $otraRuta->id]);

    $this->putJson("/api/v1/customers/{$customer->id}", [
        'name' => 'Intento',
    ], authHeaders($vendedor))->assertForbidden();
});

test('vendedor no puede reasignar customer a ruta ajena (403)', function () {
    $vendedor     = User::factory()->create(['level' => 'vendedor']);
    $otroVendedor = User::factory()->create(['level' => 'vendedor']);
    $miRuta       = LoanRoad::factory()->create(['user_id' => $vendedor->id]);
    $otraRuta     = LoanRoad::factory()->create(['user_id' => $otroVendedor->id]);
    $customer     = Customer::factory()->create(['loan_road_id' => $miRuta->id]);

    $this->putJson("/api/v1/customers/{$customer->id}", [
        'loan_road_id' => $otraRuta->id,
    ], authHeaders($vendedor))->assertForbidden();

    $this->assertDatabaseHas('customers', ['id' => $customer->id, 'loan_road_id' => $miRuta->id]);
});

test('actualizar customer sin cambiar identification no da 422', function () {
    $admin    = User::factory()->create(['level' => 'administrador']);
    $customer = Customer::factory()->create(['identification' => 12345678]);

    $this->putJson("/api/v1/customers/{$customer->id}", [
        'identification' => 12345678,
    ], authHeaders($admin))->assertOk();
});

// ── Destroy ───────────────────────────────────────────────────────────────────

test('admin puede eliminar customer', function () {
    $admin    = User::factory()->create(['level' => 'administrador']);
    $customer = Customer::factory()->create();

    $this->deleteJson("/api/v1/customers/{$customer->id}", [], authHeaders($admin))
        ->assertNoContent();

    $this->assertSoftDeleted('customers', ['id' => $customer->id]);
});

test('supervisor no puede eliminar customer (403)', function () {
    $supervisor = User::factory()->create(['level' => 'supervisor']);
    $customer   = Customer::factory()->create();

    $this->deleteJson("/api/v1/customers/{$customer->id}", [], authHeaders($supervisor))
        ->assertForbidden();
});

test('vendedor no puede eliminar customer (403)', function () {
    $vendedor = User::factory()->create(['level' => 'vendedor']);
    $ruta     = LoanRoad::factory()->create(['user_id' => $vendedor->id]);
    $customer = Customer::factory()->create(['loan_road_id' => $ruta->id]);

    $this->deleteJson("/api/v1/customers/{$customer->id}", [], authHeaders($vendedor))
        ->assertForbidden();
});
