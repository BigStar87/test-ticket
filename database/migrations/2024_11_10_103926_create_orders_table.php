<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->references('id')->on('ticket_types')->onDelete('cascade');
            $table->string('event_date');
            $table->integer('ticket_adult_price');
            $table->integer('ticket_adult_quantity');
            $table->integer('ticket_kid_price');
            $table->integer('ticket_kid_quantity');
            $table->integer('ticket_group_price')->default(0);
            $table->integer('ticket_group_quantity')->default(0);
            $table->integer('ticket_preferential_price')->default(0);
            $table->integer('ticket_preferential_quantity')->default(0);
            $table->string('barcode');
            $table->integer('equal_price');
            $table->dateTime('created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
