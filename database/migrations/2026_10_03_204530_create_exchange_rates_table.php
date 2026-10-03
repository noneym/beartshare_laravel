<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('currency', 3)->default('USD');
            $table->decimal('rate', 12, 4)->comment('1 birim döviz = ? TL (TCMB döviz satış)');
            $table->string('source', 50)->default('tcmb');
            $table->date('rate_date')->nullable()->comment('Kaynağın bülten tarihi');
            $table->unsignedInteger('artworks_updated')->default(0);
            $table->timestamps();
            $table->index(['currency', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
