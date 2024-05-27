<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStripeObjectsTable extends Migration
{
    public function up()
    {
        Schema::create('mbi_stripe.objects', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_id')->unique()->nullable();
            $table->string('object_type');
            $table->jsonb('data');
            $table->boolean('livemode');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mbi_stripe.objects');
    }
}
