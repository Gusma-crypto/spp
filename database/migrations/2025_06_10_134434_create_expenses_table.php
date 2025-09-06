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
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('date');
            $table->string('total_expense');
            $table->longText('note');
            $table->longText('file_path')->nullable();
            
            // Tambahkan kolom status validasi kepala sekolah
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestampsTz();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
