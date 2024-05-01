<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add last_event_id to mbi_stripe.objects with a foreign key constraint
        Schema::table('mbi_stripe.objects', function (Blueprint $table) {
            $table->unsignedBigInteger('last_event_id')->after('livemode')->nullable();
            $table->foreign('last_event_id')->references('id')->on('mbi_stripe.events')->onDelete('restrict');
        });

        // Add object_id to mbi_stripe.events with a foreign key constraint
        Schema::table('mbi_stripe.events', function (Blueprint $table) {
            $table->unsignedBigInteger('object_id')->after('stripe_id')->nullable();
            $table->foreign('object_id')->references('id')->on('mbi_stripe.objects')->onDelete('cascade');
        });
    }

    public function down()
    {
        // Remove the foreign key and column from mbi_stripe.events
        Schema::table('mbi_stripe.events', function (Blueprint $table) {
            $table->dropForeign(['object_id']);
            $table->dropColumn('object_id');
        });

        // Remove the foreign key and column from mbi_stripe.objects
        Schema::table('mbi_stripe.objects', function (Blueprint $table) {
            $table->dropForeign(['last_event_id']);
            $table->dropColumn('last_event_id');
        });
    }
};
