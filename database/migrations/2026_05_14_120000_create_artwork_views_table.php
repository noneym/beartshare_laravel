<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('artwork_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artwork_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_id', 100)->nullable();
            $table->timestamp('viewed_at')->useCurrent();

            $table->index(['artwork_id', 'viewed_at']);
            $table->index(['artwork_id', 'user_id', 'viewed_at']);
            $table->index(['artwork_id', 'session_id', 'viewed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('artwork_views');
    }
};
