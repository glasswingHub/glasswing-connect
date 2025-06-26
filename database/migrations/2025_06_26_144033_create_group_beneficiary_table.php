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
        Schema::create('group_beneficiary', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('fkIdBeneficiary');
            $table->unsignedInteger('fkIdGroupschool');
            $table->unsignedInteger('fkIdRazonesDesersion')->nullable();
            $table->unsignedInteger('voluntary_state_id')->nullable();
            $table->text('observation')->nullable();
            $table->date('leaveSchool')->nullable();
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
        Schema::dropIfExists('group_beneficiary');
    }
};
