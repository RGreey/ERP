<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Obtener todos los eventos
        $eventos = DB::table('evento')->get();

        foreach ($eventos as $evento) {
            $dependencias = [
                $evento->programadependencia,
                $evento->programadependenciasecundario,
                $evento->programadependenciaterciaria,
            ];

            foreach ($dependencias as $dep) {
                if ($dep) { // solo si no es null
                    // Evitar duplicados
                    DB::table('evento_dependencia')->updateOrInsert(
                        ['evento_id' => $evento->id, 'programadependencia_id' => $dep],
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Si se revierte, eliminamos todas las dependencias cargadas
        DB::table('evento_dependencia')->truncate();
    }
};
