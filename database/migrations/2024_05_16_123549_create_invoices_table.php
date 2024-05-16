<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('mbi_stripe.invoices', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('customer_id');
            $table->foreign('customer_id')->references('id')->on('mbi_stripe.customers')->onDelete('cascade');
            $table->jsonb('data');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mbi_stripe.invoices');
    }
}
