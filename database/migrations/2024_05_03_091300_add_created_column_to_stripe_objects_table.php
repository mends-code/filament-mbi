<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds a 'created' column to the 'mbi_stripe.objects' table to store the creation timestamp of the Stripe object.
     */
    public function up()
    {
        Schema::table('mbi_stripe.objects', function (Blueprint $table) {
            $table->bigInteger('created')->after('object_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Removes the 'created' column from the 'mbi_stripe.objects' table if necessary.
     */
    public function down()
    {
        Schema::table('mbi_stripe.objects', function (Blueprint $table) {
            $table->dropColumn('created');
        });
    }
};
