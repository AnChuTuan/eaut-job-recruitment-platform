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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('title', 200);
            $table->string('category', 100);
            $table->string('job_level', 50)->nullable();
            $table->string('employment_type', 50)->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->date('deadline');
            $table->decimal('salary_min', 12, 2)->nullable();
            $table->decimal('salary_max', 12, 2)->nullable();
            $table->string('currency', 10)->default('VND');
            $table->string('location', 150);
            $table->longText('description');
            $table->text('requirements')->nullable();
            $table->text('benefits')->nullable();
            $table->text('skills')->nullable();
            $table->enum('status', ['draft', 'pending', 'approved', 'rejected', 'needs_revision', 'banned'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
