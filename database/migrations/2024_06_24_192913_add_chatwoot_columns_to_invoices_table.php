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
            $table->integer('chatwoot_account_id')->storedAs("((data ->> 'metadata')::jsonb ->> 'chatwoot_account_id')::integer")->nullable();
            $table->integer('chatwoot_conversation_id')->storedAs("((data ->> 'metadata')::jsonb ->> 'chatwoot_conversation_id')::integer")->nullable();
            $table->integer('chatwoot_contact_id')->storedAs("((data ->> 'metadata')::jsonb ->> 'chatwoot_contact_id')::integer")->nullable();
            $table->integer('chatwoot_agent_id')->storedAs("((data ->> 'metadata')::jsonb ->> 'chatwoot_agent_id')::integer")->nullable();
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
            $table->dropColumn('chatwoot_account_id');
            $table->dropColumn('chatwoot_conversation_id');
            $table->dropColumn('chatwoot_contact_id');
            $table->dropColumn('chatwoot_agent_id');
        });
    }
};
