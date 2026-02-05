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
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('artwork_title')->nullable()->after('artwork_id');
            $table->string('artist_name')->nullable()->after('artwork_title');
            $table->integer('quantity')->default(1)->after('artist_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['artwork_title', 'artist_name', 'quantity']);
        });
    }
};
