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
            // Modify the metadata column to be JSON type
            $table->json('metadata')->nullable()->change();
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
            // Revert the metadata column to its original type
            $table->text('metadata')->nullable()->change();
        });
    }
};
