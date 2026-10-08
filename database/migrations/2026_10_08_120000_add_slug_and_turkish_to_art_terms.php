<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('art_terms', function (Blueprint $table) {
            // Eski sitedeki adres: /art-terms/{slug} (İngilizce başlıktan üretilir)
            $table->string('slug')->nullable()->unique()->after('title');
            $table->string('title_tr')->nullable()->after('slug');
            $table->text('description_tr')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('art_terms', function (Blueprint $table) {
            $table->dropColumn(['slug', 'title_tr', 'description_tr']);
        });
    }
};
