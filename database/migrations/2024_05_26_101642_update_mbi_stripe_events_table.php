<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::drop('mbi_stripe.events');

        Schema::create('mbi_stripe.events', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->jsonb('data');
            $table->integer('created')->storedAs('CAST(data->>\'created\' AS INTEGER)');
            $table->string('object')->storedAs('data->\'data\'->\'object\'->>\'object\'');
            $table->string('object_id')->nullable()->storedAs('data->\'data\'->\'object\'->>\'id\'');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
