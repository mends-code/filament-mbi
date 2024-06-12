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
            $table->timestamp('finalized_at')->nullable();
            $table->timestamp('kv_expires_at')->nullable();
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
            $table->dropColumn(['kv_expires_at', 'finalized_at']);
        });
    }
};
