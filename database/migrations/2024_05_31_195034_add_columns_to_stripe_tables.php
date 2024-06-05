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
        // Modify products table
        Schema::table('mbi_stripe.products', function (Blueprint $table) {
            $table->string('default_price')->nullable()
                ->storedAs('data->>\'default_price\'');
            $table->boolean('active')->nullable()
                ->storedAs('CAST(data->>\'active\' AS BOOLEAN)');
            $table->boolean('livemode')->nullable()
                ->storedAs('CAST(data->>\'livemode\' AS BOOLEAN)');
        });

        // Modify prices table
        Schema::table('mbi_stripe.prices', function (Blueprint $table) {
            $table->boolean('active')->nullable()
                ->storedAs('CAST(data->>\'active\' AS BOOLEAN)');
            $table->string('currency')->nullable()
                ->storedAs('data->>\'currency\'');
            $table->string('type')->nullable()
                ->storedAs('data->>\'type\'');
            $table->boolean('livemode')->nullable()
                ->storedAs('CAST(data->>\'livemode\' AS BOOLEAN)');
        });

        // Modify customers table
        Schema::table('mbi_stripe.customers', function (Blueprint $table) {
            $table->boolean('livemode')->nullable()
                ->storedAs('CAST(data->>\'livemode\' AS BOOLEAN)');
        });

        // Modify invoices table
        Schema::table('mbi_stripe.invoices', function (Blueprint $table) {
            $table->boolean('livemode')->nullable()
                ->storedAs('CAST(data->>\'livemode\' AS BOOLEAN)');
            $table->string('currency')->nullable()
                ->storedAs('data->>\'currency\'');
            $table->string('status')->nullable()
                ->storedAs('data->>\'status\'');
            $table->boolean('paid')->nullable()
                ->storedAs('CAST(data->>\'paid\' AS BOOLEAN)');
            $table->integer('total')->nullable()
                ->storedAs('CAST(data->>\'total\' AS INTEGER)');
        });

        // Modify events table
        Schema::table('mbi_stripe.events', function (Blueprint $table) {
            $table->boolean('livemode')->nullable()
                ->storedAs('CAST(data->>\'livemode\' AS BOOLEAN)');
        });

        // Drop the active_since column from prices table
        Schema::table('mbi_stripe.prices', function (Blueprint $table) {
            $table->dropColumn('active_since');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse modifications for products table
        Schema::table('mbi_stripe.products', function (Blueprint $table) {
            $table->dropColumn(['name', 'description', 'default_price', 'active', 'livemode']);
        });

        // Reverse modifications for prices table
        Schema::table('mbi_stripe.prices', function (Blueprint $table) {
            $table->dropColumn(['active', 'currency', 'type', 'livemode']);
            $table->integer('active_since')->nullable()
                ->storedAs('CAST(data->\'metadata\'->>\'active_since\' AS INTEGER)');
        });

        // Reverse modifications for customers table
        Schema::table('mbi_stripe.customers', function (Blueprint $table) {
            $table->dropColumn('livemode');
        });

        // Reverse modifications for invoices table
        Schema::table('mbi_stripe.invoices', function (Blueprint $table) {
            $table->dropColumn(['livemode', 'currency', 'status', 'paid', 'total']);
        });

        // Reverse modifications for events table
        Schema::table('mbi_stripe.events', function (Blueprint $table) {
            $table->dropColumn('livemode');
        });
    }
};
