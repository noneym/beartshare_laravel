<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Orders tablosuna yeni alanlar
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method')->default('havale')->after('status'); // havale, kredi_karti
            $table->string('payment_code', 20)->nullable()->after('payment_method'); // Havale açıklama kodu
            $table->string('billing_address')->nullable()->after('shipping_address');
            $table->string('city')->nullable()->after('billing_address');
            $table->string('district')->nullable()->after('city');
            $table->string('tc_no', 11)->nullable()->after('customer_phone');
            $table->timestamp('confirmed_at')->nullable()->after('notes');
        });

        // Artworks tablosuna is_reserved alanı
        Schema::table('artworks', function (Blueprint $table) {
            $table->boolean('is_reserved')->default(false)->after('is_sold');
        });

        // Users tablosuna art_puan alanı
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('art_puan', 12, 2)->default(0)->after('referred_by');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'payment_code', 'billing_address', 'city', 'district', 'tc_no', 'confirmed_at']);
        });

        Schema::table('artworks', function (Blueprint $table) {
            $table->dropColumn('is_reserved');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('art_puan');
        });
    }
};
