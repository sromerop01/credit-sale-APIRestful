<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\LoanRoad;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
{
    // Admin User
    User::factory()->create([
        'name' => 'Santiago',
        'email' => 'admin@admin.com',
        'password' => bcrypt('password'),
        'level' => 'administrador',
    ]);

    //Crear un Supervisor
    $supervisor = User::factory()->create([
        'name' => 'Santiago Supervisor',
        'email' => 'supervisor@supervisor.com',
        'password' => bcrypt('supervisorpass'),
        'level' => 'supervisor',
    ]);

    // Crear un vendedor asignado al supervisor
    $vendedorAsignado = User::factory()->create([
        'name' => 'Santiago Vendedor',
        'email' => 'vendedor@vendedor.com',
        'password' => bcrypt('vendedorpass'),
        'level' => 'vendedor',

    ]);

    // Crear una Ruta para el vendedor asignado
    $rutaAsignada = LoanRoad::factory()->create([
        'user_id' => $vendedorAsignado->id,
        'supervisor_id' => $supervisor->id
    ]);

    // Crear 20 clientes para la ruta del vendedor asignado
    Customer::factory()->count(20)->create([
        'loan_road_id' => $rutaAsignada->id
    ]);

    //Crear 5 Vendedores
    $vendedores = User::factory()->count(5)->create(['level' => 'vendedor']);

    // Para cada vendedor, crear una Ruta y asignarle Clientes
    foreach ($vendedores as $vendedor) {

        $ruta = LoanRoad::factory()->create([
            'user_id' => $vendedor->id,
            'supervisor_id' => $supervisor->id
        ]);

        // Crear 20 clientes para esta ruta específica
        Customer::factory()->count(20)->create([
            'loan_road_id' => $ruta->id
        ]);
    }
}
}
