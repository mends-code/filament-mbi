<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mbi_stripe.prices', function (Blueprint $table) {
            $table->string('id')->primary()->index();
            $table->string('product_id')->index();
            $table->foreign('product_id')->references('id')->on('mbi_stripe.products')->onDelete('cascade');
            $table->jsonb('data');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mbi_stripe.prices');
    }
};
