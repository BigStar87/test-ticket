<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('event_date');
            $table->integer('price');
            $table->timestamps();
        });

        DB::table('ticket_types')->insert([
            [
                'name' => 'adult',
                'description' => 'Взрослый билет',
                'event_date' => '31.01.01 13:30',
                'price' => 200,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'child',
                'description' => 'Детский билет',
                'event_date' => '31.01.01 13:30',
                'price' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_types');
    }
};
