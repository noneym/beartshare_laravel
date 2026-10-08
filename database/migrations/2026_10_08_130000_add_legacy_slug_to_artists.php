<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artists', function (Blueprint $table) {
            // Eski sitedeki /artists/{slug} adresi (yeni slug'dan farklı olabilir): 301 yönlendirme için
            $table->string('legacy_slug')->nullable()->index()->after('slug');
        });
    }

    public function down(): void
    {
        Schema::table('artists', function (Blueprint $table) {
            $table->dropColumn('legacy_slug');
        });
    }
};
