<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mbi_filament.chatwoot_contact_stripe_customer', function (Blueprint $table) {
            $table->unsignedBigInteger('chatwoot_contact_id');
            $table->unsignedBigInteger('stripe_customer_id'); // Ensure this is a BIGINT
            $table->foreign('chatwoot_contact_id')->references('id')->on('mbi_chatwoot.contacts')->onDelete('cascade');
            $table->foreign('stripe_customer_id')->references('id')->on('mbi_stripe.objects')->onDelete('cascade');
            $table->timestamps();
            $table->primary(['chatwoot_contact_id', 'stripe_customer_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('mbi_filament.chatwoot_contact_stripe_customer');
    }
};
