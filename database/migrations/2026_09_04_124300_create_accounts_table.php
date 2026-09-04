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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('currency', 3);

            $table->decimal('cash_balance', 15, 2)->default(0);
            $table->decimal('holdings_balance', 15, 2)->default(0);
            $table->decimal('total_balance', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
