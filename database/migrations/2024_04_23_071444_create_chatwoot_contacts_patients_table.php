<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mbi_filament.chatwoot_contacts_patients', function (Blueprint $table) {
            $table->unsignedBigInteger('chatwoot_contact_id');  // Foreign key for chatwoot_contacts
            $table->unsignedBigInteger('patient_id');  // Foreign key for patients
            $table->timestamps();

            // Set primary key on both IDs
            $table->primary(['chatwoot_contact_id', 'patient_id']);

            $table->foreign('patient_id')
                ->references('id')->on('mbi_filament.patients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatwoot_contacts_patients');
    }
};
