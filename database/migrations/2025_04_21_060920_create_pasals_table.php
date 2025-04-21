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
        Schema::create('pasals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doc_id')->constrained('documents')->onDelete('cascade');
            $table->longText('pasal');
            $table->longText('penjelasan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pasals');
    }
};
