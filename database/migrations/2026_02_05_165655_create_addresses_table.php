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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Adres tipi: shipping (teslimat) veya billing (fatura)
            $table->enum('type', ['shipping', 'billing'])->default('shipping');

            // Adres başlığı: "Ev Adresim", "İş Adresim" vs.
            $table->string('title')->nullable();

            // Kişi bilgileri
            $table->string('full_name');
            $table->string('phone')->nullable();

            // Adres bilgileri
            $table->string('city');
            $table->string('district');
            $table->text('address_line'); // Mahalle, sokak, bina no, daire no

            // Fatura bilgileri (sadece billing tipinde)
            $table->enum('invoice_type', ['individual', 'corporate'])->default('individual'); // Bireysel / Kurumsal
            $table->string('tc_no', 11)->nullable(); // Bireysel için TC
            $table->string('company_name')->nullable(); // Şirket unvanı
            $table->string('tax_office')->nullable(); // Vergi dairesi
            $table->string('tax_number')->nullable(); // Vergi numarası

            // Varsayılan adres mi?
            $table->boolean('is_default')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
