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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_no')->unique();
            $table->string('requester_name');
            $table->string('email');
            $table->string('student_no');
            $table->string('yearlvl_degree');
            $table->date('date_requested');
            $table->string('purpose');
            $table->enum('status', [
                'Pending',
                'Accepted',
                'Uploaded',
                'Ready for Release',
                'Released',
                'Declined'
            ])->default('Pending');

            $table->string('receipt_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
