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
        Schema::create('security_transaction_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transaction_id')
                ->unique()
                ->constrained()
                ->restrictOnDelete();

            $table->string('ticker', 20);
            $table->unsignedInteger('quantity');
            $table->decimal('price', 15, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_transaction_details');
    }
};
