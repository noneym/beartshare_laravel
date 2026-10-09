<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // İptal edilen siparişin beklemedeki ödemesi "cancelled" olur
        DB::statement("ALTER TABLE payment_transactions MODIFY status ENUM('pending','completed','failed','refunded','cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::table('payment_transactions')->where('status', 'cancelled')->update(['status' => 'failed']);
        DB::statement("ALTER TABLE payment_transactions MODIFY status ENUM('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending'");
    }
};
