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
        Schema::table('importer_columns', function (Blueprint $table) {
            $table->string('source_table')->nullable();
            $table->string('target_table')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('importer_columns', function (Blueprint $table) {
            $table->dropColumn('source_table');
            $table->dropColumn('target_table');
        });
    }
};
