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
            // Rename the column
            $table->renameColumn('base64_encoded_target_url', 'base64_target_url');
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
            // Rename the column back to its original name
            $table->renameColumn('base64_target_url', 'base64_encoded_target_url');
        });
    }
};
