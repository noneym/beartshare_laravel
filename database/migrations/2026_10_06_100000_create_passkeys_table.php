<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Üye passkey'leri (WebAuthn). Bir üyenin birden fazla cihazı olabilir.
 * Sunucuda yalnızca açık anahtar tutulur; gizli anahtar üyenin cihazında kalır.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('passkeys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('credential_id', 512)->unique()->comment('base64url');
            $table->text('public_key')->comment('PEM');
            $table->unsignedBigInteger('sign_count')->default(0);
            $table->string('rp_id')->comment('Oluşturulduğu alan adı');
            $table->string('aaguid', 64)->nullable()->comment('Cihaz / şifre yöneticisi türü');
            $table->boolean('backed_up')->default(false)->comment('Senkronlanan passkey (iCloud, Google vb.)');
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passkeys');
    }
};
