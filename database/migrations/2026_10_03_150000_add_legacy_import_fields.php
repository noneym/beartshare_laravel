<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Eski CodeIgniter sisteminden aktarılan verinin karşılığı olmayan alanlar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Eski sistemde şifreler tuzsuz SHA1; bcrypt(sha1(şifre)) olarak saklanır, ilk girişte yenilenir
            $table->boolean('legacy_password')->default(false)->after('password');
            $table->date('birth_date')->nullable()->after('tc_no');
        });

        Schema::table('artworks', function (Blueprint $table) {
            // Sepet/ödeme öncesi gösterilen satış notu (komisyon faturası, KDV bilgisi vb.)
            $table->text('sale_note')->nullable()->after('description');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_company')->nullable()->after('district');
            $table->string('tracking_number')->nullable()->after('shipping_company');
            $table->text('admin_notes')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['legacy_password', 'birth_date']);
        });
        Schema::table('artworks', function (Blueprint $table) {
            $table->dropColumn('sale_note');
        });
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_company', 'tracking_number', 'admin_notes']);
        });
    }
};
