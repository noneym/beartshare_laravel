<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Yönetici bildirim alıcıları (yeni sipariş, iletişim mesajı, eser başvurusu).
 * Admin > Bildirim Alıcıları sayfasından yönetilir.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_recipients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->boolean('via_email')->default(true);
            $table->boolean('via_sms')->default(false);
            $table->json('events')->comment('new_order, contact_message, artwork_submission');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Eski sistemde yeni sipariş bildirimi kodda sabit olarak bu kişilere gidiyordu
        $now = now();
        DB::table('notification_recipients')->insert([
            ['name' => 'Sinan Aydın', 'email' => 'sinana@sahukuk.com', 'phone' => '5327776008', 'via_email' => true, 'via_sms' => true, 'events' => json_encode(['new_order']), 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Osman Nuri İyem', 'email' => 'osmannuriiyem@gmail.com', 'phone' => '5327161632', 'via_email' => true, 'via_sms' => true, 'events' => json_encode(['new_order']), 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Berk Can Say', 'email' => null, 'phone' => '5054752013', 'via_email' => false, 'via_sms' => true, 'events' => json_encode(['new_order']), 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_recipients');
    }
};
