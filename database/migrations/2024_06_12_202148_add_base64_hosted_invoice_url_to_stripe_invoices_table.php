<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mbi_stripe.invoices', function (Blueprint $table) {
            // Adding the calculated column
            $table->string('base64_hosted_invoice_url')->storedAs("encode((data->>'hosted_invoice_url')::bytea, 'base64')")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mbi_stripe.invoices', function (Blueprint $table) {
            // Dropping the calculated column
            $table->dropColumn('base64_hosted_invoice_url');
        });
    }
};
