<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Create a function to handle post-event insertion logic
        DB::unprepared("
            CREATE OR REPLACE FUNCTION mbi_stripe.handle_event_insertion()
            RETURNS TRIGGER AS $$
            DECLARE
                v_object RECORD;
                v_latest_event RECORD;
            BEGIN
                -- Attempt to find an existing object with the same stripe_id
                SELECT * INTO v_object FROM mbi_stripe.objects
                WHERE stripe_id = NEW.object_id
                LIMIT 1;

                IF NOT FOUND THEN
                    -- If no object is found, create a new one
                    INSERT INTO mbi_stripe.objects (stripe_id, data, created, livemode)
                    VALUES (NEW.object_id, NEW.data->'object', NEW.created, NEW.livemode)
                    RETURNING id INTO NEW.object_id; -- Update the event with the new object's ID
                ELSE
                    -- If an object is found, update the event with the existing object's ID
                    NEW.object_id := v_object.id;

                    -- Find the latest event for this object
                    SELECT * INTO v_latest_event FROM mbi_stripe.events
                    WHERE object_id = v_object.stripe_id
                    ORDER BY created DESC
                    LIMIT 1;

                    -- Update the object with the latest event's data
                    IF FOUND THEN
                        UPDATE mbi_stripe.objects
                        SET data = v_latest_event.data->'object',
                            last_event_id = v_latest_event.id
                        WHERE id = v_object.id;
                    END IF;
                END IF;

                RETURN NEW;
            END;
            $$ LANGUAGE plpgsql;
        ");

        // Create a trigger to execute the function after inserting an event
        DB::unprepared("
            CREATE TRIGGER after_event_insertion
            AFTER INSERT ON mbi_stripe.events
            FOR EACH ROW
            EXECUTE FUNCTION mbi_stripe.handle_event_insertion();
        ");
    }

    public function down()
    {
        DB::unprepared("DROP TRIGGER IF EXISTS after_event_insertion ON mbi_stripe.events;");
        DB::unprepared("DROP FUNCTION IF EXISTS mbi_stripe.handle_event_insertion;");
    }
};
