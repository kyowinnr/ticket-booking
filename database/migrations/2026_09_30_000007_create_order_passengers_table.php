<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('order_passengers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('ticket_type_id')->constrained('ticket_types')->restrictOnDelete();
            $table->string('name', 100);
            $table->string('id_number', 30)->nullable();
            $table->date('birthday')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('gender', 20)->nullable();
            $table->timestamps();

            $table->index('id_number');
        });
    }

    public function down(): void { Schema::dropIfExists('order_passengers'); }
};
