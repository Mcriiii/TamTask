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
        Schema::create('lost_founds', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_no')->unique();
            $table->string('reporter_name');
            $table->string('email');
            $table->date('date_reported');
            $table->string('location_found')->nullable();
            $table->string('item_type');
            $table->text('description');
            $table->string('status')->default('In Progress');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lost_founds');
    }
};
