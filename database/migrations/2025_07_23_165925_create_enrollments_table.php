<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->datetime('enrolled_at');
            $table->datetime('completed_at')->nullable();
            $table->float('progress_percentage', 5, 2)->default(0.00);
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['student_id', 'course_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('enrollments');
    }
};
