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
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ✅ Add this line
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('ticket_no')->unique();
            $table->string('reporter_name');
            $table->string('student_no')->nullable();
            $table->date('date_reported');
            $table->string('yearlvl_degree')->nullable();
            $table->string('subject');
            $table->dateTime('meeting_schedule')->nullable();
            $table->string('status')->default('Pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
