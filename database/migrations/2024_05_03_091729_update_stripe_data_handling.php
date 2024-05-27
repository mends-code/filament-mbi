<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Function to populate 'created' column from existing object data
        DB::unprepared("
            CREATE OR REPLACE FUNCTION update_created_column()
            RETURNS VOID AS $$
            DECLARE
                rec RECORD;
            BEGIN
                FOR rec IN SELECT id, data FROM mbi_stripe.objects LOOP
                    IF (rec.data->'created') IS NOT NULL THEN
                        UPDATE mbi_stripe.objects
                        SET created = (rec.data->'created')::BIGINT
                        WHERE id = rec.id;
                    END IF;
                END LOOP;
            END;
            $$ LANGUAGE plpgsql;

            -- Execute the function immediately to update existing rows
            SELECT update_created_column();
            DROP FUNCTION update_created_column();
        ");

        // Modify the existing function to handle 'created' field upon insertion
        DB::unprepared("
            CREATE OR REPLACE FUNCTION mbi_stripe.process_event_insertion()
            RETURNS TRIGGER AS $$
            DECLARE
                obj_id BIGINT;
                latest_event RECORD;
                created_ts BIGINT;
            BEGIN
                -- Check for an existing object with the same stripe_id as the event's stripe_object_id
                SELECT id INTO obj_id FROM mbi_stripe.objects
                WHERE stripe_id = NEW.stripe_object_id;

                -- Handle creation time if available
                IF (NEW.data->'object'->>'created') IS NOT NULL THEN
                    created_ts := (NEW.data->'object'->>'created')::BIGINT;
                ELSE
                    created_ts := NULL;
                END IF;

                IF obj_id IS NULL THEN
                    -- Create a new object if it does not exist
                    INSERT INTO mbi_stripe.objects (stripe_id, object_type, data, livemode, created, updated_at)
                    VALUES (NEW.stripe_object_id, NEW.data->'object'->>'object', NEW.data->'object', NEW.livemode, created_ts, CURRENT_TIMESTAMP)
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
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        DB::unprepared('DROP FUNCTION IF EXISTS update_created_column;');
    }
};
