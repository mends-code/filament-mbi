<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStripeObjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::connection('pgsql')->create('mbi_stripe.objects', function (Blueprint $table) {
            $table->id();
            $table->string('object_id')->index()->nullable(); //in rare cases objects are without ids
            $table->foreignId('last_event_id')->constrained('mbi_stripe.events')->onDelete('cascade');
            $table->string('type');
            $table->jsonb('data')->nullable(); // Storing all other object details in JSONB format
            $table->bigInteger('created'); // Timestamp from the object data
            $table->boolean('livemode');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::connection('pgsql')->dropIfExists('mbi_stripe.objects');
    }
}
