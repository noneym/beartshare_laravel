<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Üye iki adımlı doğrulama (2FA): SMS ya da Authenticator (TOTP).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('two_factor_method', 10)->nullable()->after('remember_token')->comment('sms | totp; null = kapalı');
            $table->text('two_factor_secret')->nullable()->after('two_factor_method')->comment('TOTP gizli anahtarı (şifreli)');
            $table->text('two_factor_recovery_codes')->nullable()->after('two_factor_secret')->comment('Kurtarma kodları (şifreli, hash listesi)');
            $table->timestamp('two_factor_confirmed_at')->nullable()->after('two_factor_recovery_codes');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['two_factor_method', 'two_factor_secret', 'two_factor_recovery_codes', 'two_factor_confirmed_at']);
        });
    }
};
