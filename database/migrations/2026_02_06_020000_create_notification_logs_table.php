<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('channel', ['sms', 'email'])->index();
            $table->string('type')->index(); // order_created, buyer_artpuan, referrer_artpuan, favorite_reserved
            $table->string('recipient'); // telefon veya e-posta adresi
            $table->string('subject')->nullable(); // email subject
            $table->text('message'); // SMS mesaj metni veya email body (kısaltılmış)
            $table->enum('status', ['success', 'failed'])->default('failed')->index();
            $table->text('error')->nullable(); // hata mesajı
            $table->string('api_response')->nullable(); // NetGSM response kodu vs.
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
