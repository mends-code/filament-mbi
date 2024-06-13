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
        Schema::table('mbi_link_shortener.link_entries', function (Blueprint $table) {
            // Generated column for shortened link ID
            $table->text('shortened_link_id')->storedAs(
                "substring((data->'event'->'request'->>'url') from 'https?://[^/]+/([^?]*)')"
            )
            ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mbi_link_shortener.link_entries', function (Blueprint $table) {
            // Dropping the shortened_link_id column
            $table->dropColumn('shortened_link_id');
        });
    }
};
