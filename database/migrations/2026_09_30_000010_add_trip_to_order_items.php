<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('order_items', 'trip_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreignId('trip_id')->nullable()->after('order_id')->constrained('trips')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('order_passengers', 'order_item_id')) {
            Schema::table('order_passengers', function (Blueprint $table) {
                $table->foreignId('order_item_id')->nullable()->after('order_id')->constrained('order_items')->nullOnDelete();
            });
        }

        // 舊資料以訂單原本的 trip_id 回填，讓既有單程訂單仍可正常使用。
        if (Schema::hasColumn('orders', 'trip_id')) {
            \Illuminate\Support\Facades\DB::statement(
                'UPDATE order_items oi INNER JOIN orders o ON o.id = oi.order_id SET oi.trip_id = o.trip_id WHERE oi.trip_id IS NULL'
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('order_passengers', 'order_item_id')) {
            Schema::table('order_passengers', function (Blueprint $table) {
                $table->dropForeign(['order_item_id']);
                $table->dropColumn('order_item_id');
            });
        }

        if (Schema::hasColumn('order_items', 'trip_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropForeign(['trip_id']);
                $table->dropColumn('trip_id');
            });
        }
    }
};
