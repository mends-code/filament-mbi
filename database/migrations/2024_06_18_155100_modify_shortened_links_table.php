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
        Schema::table('mbi_link_shortener.shortened_links', function (Blueprint $table) {
            $table->string('chatwoot_account_id')->storedAs("metadata->>'chatwoot_account_id'")->nullable();
            $table->string('chatwoot_agent_id')->storedAs("metadata->>'chatwoot_agent_id'")->nullable();
            $table->string('chatwoot_contact_id')->storedAs("metadata->>'chatwoot_contact_id'")->nullable();
            $table->string('chatwoot_conversation_id')->storedAs("metadata->>'chatwoot_conversation_id'")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mbi_link_shortener.shortened_links', function (Blueprint $table) {
            $table->dropColumn(['chatwoot_contact_id', 'chatwoot_conversation_id', 'chatwoot_agent_id', 'chatwoot_account_id']);
        });
    }
};
