<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('mbi_stripe.products', function (Blueprint $table) {
            $table->string('id')->primary()->index();
            $table->jsonb('data');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mbi_stripe.products');
    }
};
