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
        Schema::table('mbi_stripe.customers', function (Blueprint $table) {
            $table->dropTimestamps();
            $table->integer('created')->nullable();
            $table->bigInteger('chatwoot_contact_id')->nullable()->change();
        });

        Schema::table('mbi_stripe.invoices', function (Blueprint $table) {
            $table->dropTimestamps();
            $table->integer('created')->nullable();
            $table->string('customer_id')->nullable()->change();
        });

        Schema::table('mbi_stripe.prices', function (Blueprint $table) {
            $table->dropTimestamps();
            $table->integer('created')->nullable();
            $table->string('product_id')->nullable()->change();
        });

        Schema::table('mbi_stripe.products', function (Blueprint $table) {
            $table->dropTimestamps();
            $table->integer('created')->nullable();
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
