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
        Schema::table('mbi_stripe.prices', function (Blueprint $table) {
            $table->integer('unit_amount')->nullable()
                ->storedAs('CAST(data->>\'unit_amount\' AS INTEGER)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mbi_stripe.prices', function (Blueprint $table) {
            //
        });
    }
};
