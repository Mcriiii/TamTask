<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('violations', function (Blueprint $table) {
            $table->id();
            $table->string('violation_no')->unique();
            $table->string('full_name');             
            $table->string('student_no');        
            $table->string('student_email');   
            $table->date('date_reported');         
            $table->string('yearlvl_degree');         
            $table->string('offense');               
            $table->enum('level', ['Minor', 'Major']); 
            $table->enum('status', ['Pending', 'Complete']);
            $table->enum('action_taken', ['Warning', 'DUSAP','Suspension', 'Expulsion'])->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('violations');
    }
};
