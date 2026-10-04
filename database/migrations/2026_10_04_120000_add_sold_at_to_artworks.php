<?php

use App\Models\Artwork;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->timestamp('sold_at')->nullable()->after('is_sold');
            $table->index(['is_sold', 'sold_at']);
        });

        // Mevcut satılmış eserler: satış tarihi ve satış anındaki USD fiyatı siparişlerden
        Artwork::backfillSaleData();
    }

    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->dropIndex(['is_sold', 'sold_at']);
            $table->dropColumn('sold_at');
        });
    }
};
