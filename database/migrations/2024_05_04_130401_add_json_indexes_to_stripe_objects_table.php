<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddJsonIndexesToStripeObjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mbi_stripe.objects', function (Blueprint $table) {
            // Adding a GIN index on the JSONB data column for efficient JSON operations
            $table->index(['data'], 'mbi_stripe_objects_data_index', 'gin');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mbi_stripe.objects', function (Blueprint $table) {
            $table->dropIndex(['data']);
        });
    }
}
