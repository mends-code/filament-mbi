<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesToStripeObjectsTable extends Migration
{
    public function up()
    {
        Schema::table('mbi_stripe.objects', function (Blueprint $table) {
            // Adding an index to the `stripe_id` column
            $table->index('stripe_id', 'stripe_objects_stripe_id_index');

            // Adding an index to the `object_type` column
            $table->index('object_type', 'stripe_objects_object_type_index');
        });
    }

    public function down()
    {
        Schema::table('mbi_stripe.objects', function (Blueprint $table) {
            // Removing the indexes
            $table->dropIndex('stripe_objects_stripe_id_index');
            $table->dropIndex('stripe_objects_object_type_index');
        });
    }
}
