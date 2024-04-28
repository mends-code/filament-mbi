<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStripeEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Set the schema on the connection dynamically
        Schema::connection('pgsql')->create('mbi_stripe.events', function (Blueprint $table) {
            $table->id(); // Primary key: auto-incrementing ID
            $table->string('event_id')->index(); // Index on event_id for quicker searches
            $table->string('type');
            $table->string('object_type');
            $table->string('object_id');
            $table->bigInteger('created'); // Timestamp from Stripe
            $table->boolean('livemode');
            $table->jsonb('data'); // JSONB for storing and querying JSON data efficiently
            $table->integer('pending_webhooks');
            $table->jsonb('request')->nullable(); // Optional JSONB field
            $table->timestamps(); // Laravel default timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pgsql')->dropIfExists('mbi_stripe.events');
    }
}
