<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToStripeEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mbi_stripe.events', function (Blueprint $table) {
            // Adding a GIN index on the JSONB column for efficient data querying
            $table->index(['data'], null, 'gin');

            // Adding basic indexes on other columns that might be frequently queried
            $table->index('stripe_id');
            $table->index('stripe_object_id');
            $table->index('event_type');
            $table->index('created');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mbi_stripe.events', function (Blueprint $table) {
            $table->dropIndex(['stripe_sid', 'data', 'stripe_object_id', 'event_type', 'created']);
        });
    }
}
