<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        // Tables to be updated
        $tables = ['events', 'customers', 'invoices', 'prices', 'products'];

        foreach ($tables as $table) {
            Schema::table("mbi_stripe.{$table}", function (Blueprint $table) {
                // Temporarily rename the current id column
                $table->renameColumn('id', 'old_id');
            });

            // Drop the primary key constraint from the old_id column
            DB::statement("ALTER TABLE mbi_stripe.{$table} DROP CONSTRAINT IF EXISTS {$table}_pkey");

            Schema::table("mbi_stripe.{$table}", function (Blueprint $table) {
                // Add the new id column
                $table->string('id')->nullable();
            });

            // Update the new id column with values from data->id JSON
            DB::statement("UPDATE mbi_stripe.{$table} SET id = data->>'id'");

            // Set the new id column as the primary key
            DB::statement("ALTER TABLE mbi_stripe.{$table} ADD PRIMARY KEY (id)");

            // Set the id column to not nullable
            Schema::table("mbi_stripe.{$table}", function (Blueprint $table) {
                $table->string('id')->nullable(false)->change();
            });

            // Drop the old_id column
            Schema::table("mbi_stripe.{$table}", function (Blueprint $table) {
                $table->dropColumn('old_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Tables to be updated
        $tables = ['events', 'customers', 'invoices', 'prices', 'products'];

        foreach ($tables as $table) {
            Schema::table("mbi_stripe.{$table}", function (Blueprint $table) {
                // Temporarily rename the new id column
                $table->renameColumn('id', 'new_id');
            });

            // Drop the primary key constraint from the new_id column
            DB::statement("ALTER TABLE mbi_stripe.{$table} DROP CONSTRAINT IF EXISTS {$table}_pkey");

            Schema::table("mbi_stripe.{$table}", function (Blueprint $table) {
                // Add the original id column back
                $table->id();
            });

            // Drop the new_id column
            Schema::table("mbi_stripe.{$table}", function (Blueprint $table) {
                $table->dropColumn('new_id');
            });

            // Add the primary key back to the original id column
            DB::statement("ALTER TABLE mbi_stripe.{$table} ADD PRIMARY KEY (id)");
        }
    }
};
