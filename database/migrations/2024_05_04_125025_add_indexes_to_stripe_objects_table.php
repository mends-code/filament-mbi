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
            $table->index('stripe_id');

            // Adding an index to the `object_type` column
            $table->index('object_type');
        });
    }

    public function down()
    {
        Schema::table('mbi_stripe.objects', function (Blueprint $table) {
            // Removing the indexes
            $table->dropIndex(['stripe_id', 'object_type']);
        });
    }
}
