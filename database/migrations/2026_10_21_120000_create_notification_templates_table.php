<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Admin'den düzenlenen SMS / e-posta metinleri. Kayıt yoksa koddaki varsayılan kullanılır.
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('sms_body')->nullable();
            $table->string('email_subject')->nullable();
            $table->longText('email_body')->nullable();
            $table->boolean('sms_enabled')->default(true);
            $table->boolean('email_enabled')->default(true);
            $table->timestamps();
        });

        // Favori bildirimlerinde aynı eser için tekrar gönderimi engellemek için
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->unsignedBigInteger('artwork_id')->nullable()->after('order_id')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->dropColumn('artwork_id');
        });
    }
};
