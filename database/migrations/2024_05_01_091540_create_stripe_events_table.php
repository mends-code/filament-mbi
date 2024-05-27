<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStripeEventsTable extends Migration
{
    public function up()
    {
        Schema::create('mbi_stripe.events', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_id');
            $table->string('stripe_object_id')->nullable();
            $table->string('event_type'); // This is a foreign key that references a VARCHAR/STRING primary key in 'event_types'
            $table->bigInteger('created');
            $table->boolean('livemode');
            $table->integer('pending_webhooks');
            $table->string('api_version'); // This is a foreign key that references a VARCHAR/STRING primary key in 'event_types'
            $table->jsonb('data');
            $table->jsonb('request')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mbi_stripe.events');
    }
}
