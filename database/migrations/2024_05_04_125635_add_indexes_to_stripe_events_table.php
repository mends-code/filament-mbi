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
            $table->index(['data'], 'data_gin_idx', 'gin');
            
            // Adding basic indexes on other columns that might be frequently queried
            $table->index('stripe_id', 'stripe_id_idx');
            $table->index('stripe_object_id', 'stripe_object_id_idx');
            $table->index('event_type', 'event_type_idx');
            $table->index('created', 'created_idx');
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
            $table->dropIndex('data_gin_idx');
            $table->dropIndex('stripe_id_idx');
            $table->dropIndex('stripe_object_id_idx');
            $table->dropIndex('event_type_idx');
            $table->dropIndex('created_idx');
        });
    }
}
