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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->string('registration_number', 50)->unique();
            $table->foreignId('department_id')->constrained()->onDelete('restrict');
            $table->foreignId('programme_id')->constrained()->onDelete('restrict');
            $table->string('phone', 20)->nullable();
            $table->date('dob')->nullable();
            $table->timestamps();

            $table->index(['department_id', 'programme_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
