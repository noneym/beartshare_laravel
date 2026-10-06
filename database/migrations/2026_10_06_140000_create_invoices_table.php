<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Faturalar (Paraşüt satış faturaları). Bir fatura birden fazla siparişi kapsayabilir
 * (invoice_order), aynı siparişe alıcı ve satıcı komisyon faturaları ayrı ayrı kesilebilir.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('parasut_id')->nullable()->unique()->comment('Paraşüt sales_invoice id');
            $table->string('invoice_no')->nullable()->index();
            $table->date('issue_date')->index();
            $table->string('description')->nullable();

            // Faturanın kesildiği kişi / kurum
            $table->string('contact_name');
            $table->string('contact_tax_number', 20)->nullable()->index()->comment('TC ya da VKN');
            $table->string('contact_type', 20)->nullable()->comment('person | company');
            $table->string('party', 10)->nullable()->comment('buyer (alıcı) | seller (satıcı / konsinye sahibi)');

            // Tutarlar (TL)
            $table->decimal('net_total', 14, 2)->comment('KDV hariç');
            $table->decimal('vat_total', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->comment('KDV dahil');
            $table->string('currency', 5)->default('TRY');
            $table->json('lines')->nullable()->comment('[{name, quantity, unit_price, vat_rate, total}]');

            // e-Belge
            $table->string('e_document_type', 20)->nullable()->comment('e_archive | e_invoice');
            $table->string('e_document_id')->nullable();
            $table->string('e_document_status', 30)->nullable();
            $table->string('e_document_uuid')->nullable();
            $table->string('pdf_path')->nullable()->comment('Yükleme diskinde (R2), herkese açık değil');

            // Siparişle eşleştirme
            $table->string('match_method', 20)->nullable()->index()->comment('code | order_ref | tax_number | artwork | manual');
            $table->string('match_note')->nullable();

            $table->timestamp('synced_at')->nullable();
            $table->timestamps();
        });

        Schema::create('invoice_order', function (Blueprint $table) {
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->primary(['invoice_id', 'order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_order');
        Schema::dropIfExists('invoices');
    }
};
