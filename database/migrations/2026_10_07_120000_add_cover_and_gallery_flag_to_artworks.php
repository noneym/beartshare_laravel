<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            // Listeleme sayfalarında ilk görselin yerine gösterilen, isteğe bağlı kapak fotoğrafı
            $table->string('cover_image')->nullable()->after('images');
            // Sanal galeriye (3D sergi) dahil edilmez
            $table->boolean('hide_from_gallery')->default(false)->after('allow_credit_card');
        });
    }

    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->dropColumn(['cover_image', 'hide_from_gallery']);
        });
    }
};
