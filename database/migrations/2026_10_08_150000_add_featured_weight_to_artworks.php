<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            // Öne çıkan eserlerin sıralama ağırlığı: yüksek olan önce gösterilir
            $table->unsignedInteger('featured_weight')->default(0)->after('is_featured')->index();
        });
    }

    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->dropColumn('featured_weight');
        });
    }
};
