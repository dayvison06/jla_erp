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
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('client')->nullable();
            $table->string('technical_manager')->nullable();
            $table->date('start_date')->nullable();
            $table->date('expected_end_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('pending'); // pending, active, completed, canceled
            $table->decimal('total_area', 10, 2)->nullable();
            $table->string('type')->nullable(); // residential, commercial, industrial
            $table->text('address')->nullable();
            $table->text('description')->nullable();
            
            // Should probably track who created it
            // $table->foreignId('user_id')->constrained(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('works');
    }
};
