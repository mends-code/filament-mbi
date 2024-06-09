<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mbi_stripe.events', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('mbi_stripe.products', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('mbi_stripe.prices', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('mbi_stripe.customers', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table('mbi_stripe.invoices', function (Blueprint $table) {
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('mbi_stripe.events', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('mbi_stripe.products', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('mbi_stripe.prices', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('mbi_stripe.customers', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table('mbi_stripe.invoices', function (Blueprint $table) {
            $table->dropTimestamps();
        });
    }
};
