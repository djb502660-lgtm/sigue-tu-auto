<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Recibido', 'slug' => 'recibido'],
            ['name' => 'En diagnóstico', 'slug' => 'en-diagnostico'],
            ['name' => 'En reparación', 'slug' => 'en-reparacion'],
            ['name' => 'En pruebas', 'slug' => 'en-pruebas'],
            ['name' => 'Listo para entrega', 'slug' => 'listo-para-entrega'],
            ['name' => 'Entregado', 'slug' => 'entregado'],
        ];

        foreach ($statuses as $status) {
            Status::firstOrCreate(['slug' => $status['slug']], $status);
        }
    }
}

