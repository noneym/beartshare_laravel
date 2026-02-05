<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Orders tablosuna artpuan_used kolonu ekle
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('artpuan_used', 12, 2)->default(0)->after('total_usd');
            $table->decimal('discount_tl', 12, 2)->default(0)->after('artpuan_used');
        });

        // art_puan_logs type enum'ına 'spend' ekle
        DB::statement("ALTER TABLE art_puan_logs MODIFY COLUMN type ENUM('purchase', 'referral', 'bonus', 'manual', 'refund', 'spend') DEFAULT 'purchase'");
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['artpuan_used', 'discount_tl']);
        });

        DB::statement("ALTER TABLE art_puan_logs MODIFY COLUMN type ENUM('purchase', 'referral', 'bonus', 'manual', 'refund') DEFAULT 'purchase'");
    }
};
