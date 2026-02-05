<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->unsignedInteger('old_id')->nullable()->after('id');
            $table->string('type', 50)->default('wholesale')->after('is_featured'); // wholesale, shared
            $table->string('tags')->nullable()->after('description');
            $table->integer('sort_order')->default(0)->after('type');
        });
    }

    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->dropColumn(['old_id', 'type', 'tags', 'sort_order']);
        });
    }
};
