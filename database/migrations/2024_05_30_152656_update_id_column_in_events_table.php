<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mbi_stripe.events', function (Blueprint $table) {
            $table->dropColumn('id'); // Drop the existing id column
        });

        Schema::table('mbi_stripe.events', function (Blueprint $table) {
            $table->string('id')->storedAs("data->>'id'")->primary()->index(); // Add the new computed id column
        });
    }

    public function down()
    {
        Schema::table('mbi_stripe.events', function (Blueprint $table) {
            $table->dropColumn('id'); // Drop the computed id column
        });

        Schema::table('mbi_stripe.events', function (Blueprint $table) {
            $table->string('id')->primary()->index(); // Add back the original id column
        });
    }
};
