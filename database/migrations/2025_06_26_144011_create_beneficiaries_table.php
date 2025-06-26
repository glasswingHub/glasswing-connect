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
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->string('DNI', 30)->nullable();
            $table->string('code', 100)->nullable();
            $table->string('codGenerate', 50)->nullable();
            $table->year('year')->nullable();
            $table->string('name', 70)->nullable();
            $table->string('surname', 70)->nullable();
            $table->date('fechaNac')->nullable();
            $table->string('email_participante')->nullable();
            $table->string('phone_participante')->nullable();
            $table->unsignedInteger('genre_id')->nullable();
            $table->string('responsable', 150)->nullable();
            $table->string('phone', 25)->nullable();
            $table->unsignedInteger('fkIdRelation')->nullable();
            $table->string('fkCodeCountry', 15)->nullable();
            $table->string('fkCodeState', 15)->nullable();
            $table->string('fkCodeMunicipality', 15)->nullable();
            $table->string('address', 200)->nullable();
            $table->unsignedInteger('fkIdTypeBeneficiarity')->nullable();
            $table->integer('voice_image')->nullable();
            $table->smallInteger('casa_glasswing')->nullable();
            $table->boolean('voice_image_responsable')->nullable()->comment('0 = no, 1: si');
            $table->string('dni_responsable')->nullable();
            $table->boolean('autorizacion_responsable')->nullable()->comment('0 = no, 1: si');
            $table->integer('idFormBuilder')->nullable();
            $table->integer('elegibilidad')->nullable();
            $table->string('CE_procedencia', 150)->nullable();
            $table->string('grado_CEProcedencia', 50)->nullable();
            $table->integer('persona_institucional')->nullable();
            $table->unsignedInteger('institutional_person_id')->nullable();
            $table->unsignedInteger('beneficiaries_subtype_id')->nullable();
            $table->string('otro_tipo_persona', 180)->nullable();
            $table->string('institucion', 180)->nullable();
            $table->unsignedInteger('last_grade_id')->nullable();
            $table->unsignedInteger('migration_status_id')->nullable();
            $table->integer('razon_migration')->nullable();
            $table->integer('familiar_retornado')->nullable();
            $table->unsignedInteger('migration_family_id')->nullable();
            $table->unsignedInteger('migration_person_id')->nullable();
            $table->unsignedInteger('migration_country_id')->nullable();
            $table->string('otroPais', 199)->nullable();
            $table->integer('is_imported')->default(0)->comment('0 = no, 1 = si');
            $table->integer('is_GOES')->nullable()->comment('1 = si, 2= no');
            $table->integer('first_year_of_participation')->nullable()->comment('1= si, 2= no');
            $table->timestamp('date_imported')->nullable();
            $table->unsignedInteger('imported_by')->nullable()->comment('user id si el registro fue creado por importacion');
            $table->integer('current_year')->nullable()->comment('0 = menores a 2022, NULL = no se asigno año, de ahi en adelante los beneficiarios tomaran el año actual');
            $table->year('year_bk')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};
