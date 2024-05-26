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
            // Drop the existing chatwoot_contact_id column
            $table->dropColumn('chatwoot_contact_id');
        });

        Schema::table('mbi_stripe.customers', function (Blueprint $table) {
            // Recreate the chatwoot_contact_id column with the correct definition
            $table->bigInteger('chatwoot_contact_id')->nullable()
                ->storedAs('CAST(data->\'metadata\'->>\'chatwoot_contact_id\' AS INTEGER)');
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
