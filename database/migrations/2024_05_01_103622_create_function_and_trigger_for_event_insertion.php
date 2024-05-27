<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Create or replace the function that processes event insertions
        DB::unprepared("
            CREATE OR REPLACE FUNCTION mbi_stripe.process_event_insertion()
            RETURNS TRIGGER AS $$
            DECLARE
                obj_id BIGINT;
                latest_event RECORD;
            BEGIN
                -- Check for an existing object with the same stripe_id as the event's stripe_object_id
                SELECT id INTO obj_id FROM mbi_stripe.objects
                WHERE stripe_id = NEW.stripe_object_id;

                IF obj_id IS NULL THEN
                    -- Create a new object if it does not exist
                    INSERT INTO mbi_stripe.objects (stripe_id, object_type, data, livemode, updated_at)
                    VALUES (NEW.stripe_object_id, NEW.data->'object'->>'object', NEW.data->'object', NEW.livemode, CURRENT_TIMESTAMP)
                    RETURNING id INTO obj_id;
                END IF;

                -- Update the event with the object's ID
                UPDATE mbi_stripe.events SET object_id = obj_id WHERE id = NEW.id;

                -- Find the latest event for this object
                SELECT * INTO latest_event FROM mbi_stripe.events
                WHERE stripe_object_id = NEW.stripe_object_id
                ORDER BY created DESC LIMIT 1;

                -- Update the object with the latest event data if a newer one exists
                IF FOUND THEN
                    UPDATE mbi_stripe.objects
                    SET data = latest_event.data->'object', last_event_id = latest_event.id,
                        livemode = latest_event.livemode, updated_at = CURRENT_TIMESTAMP
                    WHERE id = obj_id;
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // Create a trigger to execute the function after inserting an event
        DB::unprepared('
            CREATE OR REPLACE TRIGGER after_insert_stripe_event
            AFTER INSERT ON mbi_stripe.events
            FOR EACH ROW
            EXECUTE FUNCTION mbi_stripe.process_event_insertion();
        ');
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS after_insert_stripe_event ON mbi_stripe.events;');
        DB::unprepared('DROP FUNCTION IF EXISTS mbi_stripe.process_event_insertion;');
    }
};
