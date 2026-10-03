<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artwork_submissions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->text('admin_note')->nullable()->after('artwork_id');
        });
    }

    public function down(): void
    {
        Schema::table('artwork_submissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
        });

        Schema::table('favorites', function (Blueprint $table) {
            $table->dropColumn('admin_note');
        });
    }
};
