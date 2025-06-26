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
        Schema::create('school_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->string('fkCode', 25)->nullable();
            $table->unsignedInteger('fkIdBeneficiary')->comment('1= active, 2= inactive, 3= sin matricular');
            $table->unsignedInteger('fkIdSection')->nullable();
            $table->unsignedInteger('fkIdLevel')->nullable();
            $table->unsignedInteger('school_beneficiaries_turn_id')->nullable();
            $table->unsignedInteger('school_beneficiaries_state_id')->nullable();
            $table->unsignedInteger('razon_reingreso_id')->nullable();
            $table->unsignedInteger('razon_desercion_id')->nullable();
            $table->text('observations')->nullable();
            $table->boolean('documentacion_inscripcion')->nullable();
            $table->timestamp('date_state')->nullable();
            $table->unsignedInteger('school_beneficiaries_approved_id')->nullable();
            $table->integer('nuevo')->default(0);
            $table->timestamp('encrypted_at')->nullable();
            $table->integer('encrypted_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_beneficiaries');
    }
};
