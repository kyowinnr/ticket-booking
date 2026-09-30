<?php

use IlluminateDatabaseMigrationsMigration;
use IlluminateDatabaseSchemaBlueprint;
use IlluminateSupportFacadesSchema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained('routes')->cascadeOnDelete();
            $table->foreignId('ship_id')->constrained('ships')->restrictOnDelete();
            $table->date('departure_date');
            $table->time('departure_time');
            $table->time('arrival_time')->nullable();
            $table->unsignedInteger('capacity');
            $table->unsignedInteger('booked_count')->default(0);
            $table->enum('status', ['open', 'closed', 'cancelled'])->default('open');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['departure_date', 'status']);
            $table->unique(['ship_id', 'departure_date', 'departure_time']);
        });
    }

    public function down(): void { Schema::dropIfExists('trips'); }
};
