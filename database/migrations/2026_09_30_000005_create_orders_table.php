<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 30)->unique();
            $table->foreignId('trip_id')->constrained('trips')->restrictOnDelete();
            $table->string('contact_name', 100);
            $table->string('contact_phone', 30);
            $table->string('contact_email', 150)->nullable();
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->enum('payment_status', ['unpaid', 'paid', 'refunded'])->default('unpaid');
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['trip_id', 'status']);
            $table->index('contact_phone');
        });
    }

    public function down(): void { Schema::dropIfExists('orders'); }
};
