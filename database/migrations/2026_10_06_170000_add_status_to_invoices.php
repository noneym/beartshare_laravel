<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Faturaları admin'den Paraşüt API ile oluşturma: taslak → resmileştirme (e-Arşiv / e-Fatura).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('status', 20)->default('issued')->after('id')->index()
                ->comment('draft (Paraşüt taslak) | formalizing (gönderiliyor) | issued (resmileşti) | failed');
            $table->string('parasut_contact_id')->nullable()->after('parasut_id');
            $table->string('trackable_job_id')->nullable()->after('e_document_uuid');
            $table->text('error')->nullable()->after('trackable_job_id');
            $table->boolean('payment_recorded')->default(false)->after('total');
            $table->foreignId('created_by')->nullable()->after('match_note')->constrained('users')->nullOnDelete();
        });

        // İçe aktarılanların hepsi resmileşmiş (e-belgesi var)
        DB::table('invoices')->whereNull('e_document_id')->update(['status' => 'draft']);
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('created_by');
            $table->dropColumn(['status', 'parasut_contact_id', 'trackable_job_id', 'error', 'payment_recorded']);
        });
    }
};
