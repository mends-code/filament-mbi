<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccountIdToContacts extends Migration
{
    public function up()
    {
        Schema::table('chatwoot_contacts', function (Blueprint $table) {
            $table->foreignId('chatwoot_account_id')->nullable()->constrained('chatwoot_accounts')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('chatwoot_contacts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('chatwoot_account_id');
        });
    }
}
