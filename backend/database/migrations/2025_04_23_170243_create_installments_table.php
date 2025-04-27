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
        Schema::create('installments', function (Blueprint $table) {
            $table->id(); // Clave primaria autoincremental
            $table->foreignId('credit_application_id')->constrained('credit_applications'); 
            $table->integer('installment_number'); 
            $table->decimal('amount', 10, 2); 
            $table->date('due_date'); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instalments');
    }
};
