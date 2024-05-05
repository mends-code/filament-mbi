<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Objects table indexes
        DB::statement('ALTER INDEX objects_pkey RENAME TO mbi_stripe_objects_id_btree_idx');
        DB::statement('ALTER INDEX stripe_objects_stripe_id_index RENAME TO mbi_stripe_objects_stripe_id_btree_idx');
        DB::statement('ALTER INDEX stripe_objects_object_type_index RENAME TO mbi_stripe_objects_object_type_btree_idx');
        DB::statement('ALTER INDEX stripe_objects_data_gin_idx RENAME TO mbi_stripe_objects_data_gin_idx');

        // Events table indexes
        DB::statement('ALTER INDEX events_pkey RENAME TO mbi_stripe_events_id_btree_idx');
        DB::statement('ALTER INDEX data_gin_idx RENAME TO mbi_stripe_events_data_gin_idx');
        DB::statement('ALTER INDEX stripe_id_idx RENAME TO mbi_stripe_events_stripe_id_btree_idx');
        DB::statement('ALTER INDEX stripe_object_id_idx RENAME TO mbi_stripe_events_stripe_object_id_btree_idx');
        DB::statement('ALTER INDEX event_type_idx RENAME TO mbi_stripe_events_event_type_btree_idx');
        DB::statement('ALTER INDEX created_idx RENAME TO mbi_stripe_events_created_btree_idx');
    }

    public function down()
    {
        // Objects table indexes
        DB::statement('ALTER INDEX mbi_stripe_objects_id_btree_idx RENAME TO objects_pkey');
        DB::statement('ALTER INDEX mbi_stripe_objects_stripe_id_btree_idx RENAME TO stripe_objects_stripe_id_index');
        DB::statement('ALTER INDEX mbi_stripe_objects_object_type_btree_idx RENAME TO stripe_objects_object_type_index');
        DB::statement('ALTER INDEX mbi_stripe_objects_data_gin_idx RENAME TO stripe_objects_data_gin_idx');

        // Events table indexes
        DB::statement('ALTER INDEX mbi_stripe_events_id_btree_idx RENAME TO events_pkey');
        DB::statement('ALTER INDEX mbi_stripe_events_data_gin_idx RENAME TO data_gin_idx');
        DB::statement('ALTER INDEX mbi_stripe_events_stripe_id_btree_idx RENAME TO stripe_id_idx');
        DB::statement('ALTER INDEX mbi_stripe_events_stripe_object_id_btree_idx RENAME TO stripe_object_id_idx');
        DB::statement('ALTER INDEX mbi_stripe_events_event_type_btree_idx RENAME TO event_type_idx');
        DB::statement('ALTER INDEX mbi_stripe_events_created_btree_idx RENAME TO created_idx');
    }
};
