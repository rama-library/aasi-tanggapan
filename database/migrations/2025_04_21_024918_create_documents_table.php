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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('no_document');
            $table->string('slug')->unique();
            $table->string('perihal');
            $table->foreignId('user_id')->nullable();
            $table->date('due_date');
            $table->time('due_time');
            $table->date('review_due_date')->nullable();
            $table->time('review_due_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
