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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('birth_date')->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('civil_state', 20)->nullable();
            $table->string('nationality', 50)->nullable();
            $table->string('birthplace', 50)->nullable();
            $table->string('cpf', 11)->unique();
            $table->string('rg', 20)->nullable();
            $table->string('cnpj', 14)->nullable();
            $table->string('issuing_agency', 20)->nullable();
            $table->date('issue_date')->nullable();
            $table->enum('escolarity', ['Ensino Fundamental', 'Ensino Médio', 'Ensino Superior Incompleto', 'Ensino Superior Completo', 'Pós-Graduação (Especialização/MBA)', 'Mestrado', 'Doutorado'])->nullable();
            $table->string('voter_registration', 20)->nullable();
            $table->string('military_certificate', 20)->nullable();
            $table->string('mother_name', 100)->nullable();
            $table->string('father_name', 100)->nullable();
            $table->text('photo')->nullable();
            $table->enum('status', ['active', 'inactive', 'on_vacation', 'on_leave', 'terminated'])->default('active');

            // Labor Documents
            $table->string('ctps_number')->nullable();
            $table->string('ctps_series')->nullable();
            $table->string('ctps_state', 2)->nullable();
            $table->string('pis_pasep')->nullable();
            $table->string('nit')->nullable();
            $table->string('cnh')->nullable();
            $table->string('cnh_category', 5)->nullable();
            $table->date('cnh_expiry')->nullable();
            $table->string('professional_registration')->nullable();

            // Endereço e Contato
            $table->string('postal_code', 10)->nullable();
            $table->string('street', 100)->nullable();
            $table->string('number', 10)->nullable();
            $table->string('complement', 50)->nullable();
            $table->string('district', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('state', 2)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('emergency_contact', 100)->nullable();
            $table->string('emergency_phone', 20)->nullable();

            // Dados bancários
            $table->string('bank')->nullable();
            $table->string('agency')->nullable();
            $table->string('account')->nullable();
            $table->string('account_type')->nullable();
            $table->string('pix_key')->nullable();

            // Dados contratuais
            $table->string('role')->nullable();
            $table->string('department')->nullable();
            $table->string('contract_type')->nullable();
            $table->date('admission_date')->nullable();
            $table->date('termination_date')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->string('work_schedule')->nullable();

            // Health and Safety
            $table->date('last_exam_date')->nullable();
            $table->date('next_exam_date')->nullable();
            $table->string('aso_result')->nullable();
            $table->text('allergies')->nullable();
            $table->string('blood_type', 5)->nullable();
            $table->text('accident_history')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('employee_benefits', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('employee_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('name');
            $table->decimal('base_salary', 10, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_has_benefits', function (Blueprint $table) {
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('benefit_id')->constrained('employee_benefits')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('employee_departaments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_dependents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('name');
            $table->string('relationship');
            $table->date('birth_date');
            $table->string('cpf', 14)->nullable();
            $table->string('rg', 20)->nullable();
            $table->string('issuing_agency', 20)->nullable();
            $table->date('issue_date')->nullable();
            $table->boolean('is_income_tax_dependent')->default(false);
            $table->boolean('is_health_plan_dependent')->default(false);
            $table->string('civil_state', 20)->nullable();
            $table->timestamps();
        });

        Schema::create('employee_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('name');
            $table->string('type');
            $table->text('path');
            $table->bigInteger('size');
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_job_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('position');
            $table->string('department');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_job_histories');
        Schema::dropIfExists('employee_has_benefits');
        Schema::dropIfExists('employee_roles');
        Schema::dropIfExists('employee_attachments');
        Schema::dropIfExists('employee_documents');
        Schema::dropIfExists('employee_dependents');
        Schema::dropIfExists('employee_benefits');
        Schema::dropIfExists('employees');
    }
};
