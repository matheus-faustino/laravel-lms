<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->onDelete('cascade');
            $table->foreignId('lesson_id')->constrained()->onDelete('cascade');
            $table->boolean('completed')->default(false);
            $table->datetime('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['enrollment_id', 'lesson_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('progress');
    }
};
