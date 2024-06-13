<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::table('mbi_link_shortener.link_entries', function (Blueprint $table) {
            // Dropping the id column
            $table->dropColumn(['id', 'shortened_link_id']);
        });

        Schema::table('mbi_link_shortener.link_entries', function (Blueprint $table) {
            $table->string('shortened_link_id')->nullable();
        });

        // Copy the data from the old storedAs column to the new string column
        DB::statement("
            UPDATE mbi_link_shortener.link_entries
            SET shortened_link_id = substring((data->'event'->'request'->>'url') from 'https?://[^/]+/([^?]*)')
            WHERE shortened_link_id IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mbi_link_shortener.link_entries', function (Blueprint $table) {
            // Dropping the id column
            $table->dropColumn(['shortened_link_id']);
        });

        // Revert shortened_link_id to a generated column
        Schema::table('mbi_link_shortener.link_entries', function (Blueprint $table) {
            $table->text('shortened_link_id')->storedAs(
                "substring((data->'event'->'request'->>'url') from 'https?://[^/]+/([^?]*)')"
            )->nullable();
        });

        // Add back the id column
        Schema::table('mbi_link_shortener.link_entries', function (Blueprint $table) {
            $table->increments('id');
        });
    }
};
