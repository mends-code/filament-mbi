<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('mbi_stripe.customers', function (Blueprint $table) {
            $table->unsignedBigInteger('chatwoot_contact_id')->nullable()->after('id');
            $table->foreign('chatwoot_contact_id')->references('id')->on('mbi_chatwoot.contacts')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('mbi_stripe.customers', function (Blueprint $table) {
            $table->dropForeign(['chatwoot_contact_id']);
            $table->dropColumn('chatwoot_contact_id');
        });
    }
};
