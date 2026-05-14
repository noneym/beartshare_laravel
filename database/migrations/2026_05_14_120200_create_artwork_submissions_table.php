<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artwork_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('phone', 30);
            $table->string('email', 200);
            $table->string('artist_name', 255);
            $table->string('artwork_title', 255);
            $table->string('technique', 100)->nullable();
            $table->string('dimensions', 100)->nullable();
            $table->string('year', 10)->nullable();
            $table->string('expected_price', 50)->nullable();
            $table->text('notes')->nullable();
            $table->json('images')->nullable();
            $table->string('status', 20)->default('new');
            $table->text('admin_notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artwork_submissions');
    }
};
