<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SetupStripeEventTriggers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // SQL to create function
        DB::unprepared("
            CREATE OR REPLACE FUNCTION update_or_create_object()
            RETURNS TRIGGER AS $$
            DECLARE
                v_existing_event_id BIGINT;
                v_object_data JSONB;
            BEGIN
                -- Extract the nested object JSON from the incoming event data
                v_object_data := NEW.data->'object';

                -- Check for the latest event for the same object_id
                SELECT event_id INTO v_existing_event_id
                FROM mbi_stripe.objects
                WHERE object_id = NEW.object_id AND created < NEW.created
                ORDER BY created DESC
                LIMIT 1;

                IF FOUND THEN
                    -- Update the existing object with new data from the latest event
                    UPDATE mbi_stripe.objects
                    SET data = v_object_data,
                        created = NEW.created,
                        livemode = NEW.livemode,
                        last_event_id = NEW.id
                    WHERE event_id = v_existing_event_id;
                ELSE
                    -- Insert a new object as no existing one matches the conditions
                    INSERT INTO mbi_stripe.objects (object_id, last_event_id, type, data, created, livemode)
                    VALUES (NEW.object_id, NEW.id, NEW.object_type, v_object_data, NEW.created, NEW.livemode);
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // SQL to create trigger
        DB::unprepared("
            CREATE TRIGGER after_event_insert
            AFTER INSERT ON mbi_stripe.events
            FOR EACH ROW
            EXECUTE PROCEDURE update_or_create_object();
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop trigger
        DB::unprepared("DROP TRIGGER IF EXISTS after_event_insert ON mbi_stripe.events;");
        
        // Drop function
        DB::unprepared("DROP FUNCTION IF EXISTS update_or_create_object;");
    }
}
