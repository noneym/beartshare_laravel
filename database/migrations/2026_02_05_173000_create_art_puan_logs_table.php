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
        Schema::create('art_puan_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('artwork_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('source_user_id')->nullable()->constrained('users')->nullOnDelete(); // referans eden kişi
            $table->enum('type', ['purchase', 'referral', 'bonus', 'manual', 'refund'])->default('purchase');
            $table->decimal('amount', 10, 2); // pozitif = kazanç, negatif = düşme
            $table->decimal('balance_after', 10, 2)->default(0); // işlem sonrası toplam bakiye
            $table->string('description')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('art_puan_logs');
    }
};
