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
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->string('transaction_id')->unique()->comment('Garanti OrderID');
            $table->string('gateway')->default('garanti')->comment('Ödeme sağlayıcı');
            $table->decimal('amount', 12, 2)->comment('İşlem tutarı');
            $table->string('currency', 3)->default('TRY');
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->integer('installment_count')->default(0)->comment('Taksit sayısı');
            $table->string('gateway_transaction_id')->nullable()->comment('Garanti TransID');
            $table->string('auth_code')->nullable()->comment('Banka onay kodu');
            $table->string('host_ref_num')->nullable()->comment('Referans numarası');
            $table->string('card_number')->nullable()->comment('Maskeli kart numarası');
            $table->string('error_code')->nullable();
            $table->text('error_message')->nullable();
            $table->json('request_data')->nullable()->comment('Gönderilen parametreler');
            $table->json('response_data')->nullable()->comment('Dönen yanıt');
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index('gateway_transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
    }
};
