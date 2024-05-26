<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('mbi_stripe.customers', function (Blueprint $table) {
            $table->dropColumn('created');
        });

        Schema::table('mbi_stripe.customers', function (Blueprint $table) {
            $table->integer('created')->storedAs('CAST(data->>\'created\' AS INTEGER)');
            $table->bigInteger('chatwoot_contact_id')->nullable()->change();
        });

        Schema::table('mbi_stripe.invoices', function (Blueprint $table) {
            $table->dropColumn('created');
        });

        Schema::table('mbi_stripe.invoices', function (Blueprint $table) {
            $table->integer('created')->storedAs('CAST(data->>\'created\' AS INTEGER)');
            $table->dropColumn('customer_id');
        });

        Schema::table('mbi_stripe.invoices', function (Blueprint $table) {
            $table->string('customer_id')->nullable()->storedAs('data->>\'customer\'');
        });

        Schema::table('mbi_stripe.prices', function (Blueprint $table) {
            $table->dropColumn('created');
        });

        Schema::table('mbi_stripe.prices', function (Blueprint $table) {
            $table->integer('created')->storedAs('CAST(data->>\'created\' AS INTEGER)');
            $table->dropColumn('product_id');
        });

        Schema::table('mbi_stripe.prices', function (Blueprint $table) {
            $table->string('product_id')->nullable()->storedAs('data->>\'product\'');
        });

        Schema::table('mbi_stripe.products', function (Blueprint $table) {
            $table->dropColumn('created');
        });

        Schema::table('mbi_stripe.products', function (Blueprint $table) {
            $table->integer('created')->storedAs('CAST(data->>\'created\' AS INTEGER)');
        });

        Schema::create('mbi_stripe.events', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->jsonb('data');
            $table->integer('created')->storedAs('CAST(data->>\'created\' AS INTEGER)');
            $table->string('object')->storedAs('data->\'object\'->>\'object\'');
            $table->string('object_id')->storedAs('data->\'object\'->>\'id\'')->nullable();
            $table->timestamps();
        });
    }
};
