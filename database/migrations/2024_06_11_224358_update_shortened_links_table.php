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
            $table->dropColumn('kv_expires_at');
            $table->renameColumn('target_url', 'base64_encoded_target_url');
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
            $table->timestamp('kv_expires_at')->nullable();
            $table->renameColumn('base64_encoded_target_url', 'target_url');
        });
    }
};
