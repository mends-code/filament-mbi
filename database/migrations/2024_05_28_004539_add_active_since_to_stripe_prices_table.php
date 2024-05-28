<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mbi_stripe.prices', function (Blueprint $table) {
            $table->integer('active_since')->nullable()
                ->storedAs('CAST(data->\'metadata\'->>\'active_since\' AS INTEGER)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mbi_stripe.prices', function (Blueprint $table) {
            $table->dropColumn('active_since');
        });
    }
};
