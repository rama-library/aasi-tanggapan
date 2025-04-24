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
        Schema::create('responds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doc_id')->constrained('documents')->onDelete('cascade');
            $table->foreignId('pasal_id')->constrained('pasals')->onDelete('cascade');
            $table->foreignId('pic_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->longText('tanggapan')->nullable(); // Bisa null jika reviewer hanya menghapus
            $table->string('perusahaan')->nullable(); // Bisa diisi otomatis dari user
            $table->text('alasan')->nullable(); // Alasan jika reviewer ubah/hapus
            $table->boolean('is_deleted')->default(false);
            $table->json('original_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('responds');
    }
};
