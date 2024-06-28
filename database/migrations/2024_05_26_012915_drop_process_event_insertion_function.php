<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('DROP FUNCTION IF EXISTS process_event_insertion() CASCADE');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {}
};
