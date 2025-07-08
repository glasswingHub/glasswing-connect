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
            $table->string('display_name')->nullable()->after('target_column');
            $table->boolean('primary_key')->default(false)->after('display_name');
            $table->boolean('show_in_list')->default(true)->after('primary_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('importer_columns', function (Blueprint $table) {
            $table->dropColumn(['display_name', 'primary_key', 'show_in_list']);
        });
    }
};
