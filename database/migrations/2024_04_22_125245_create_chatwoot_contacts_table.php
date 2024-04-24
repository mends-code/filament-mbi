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
        Schema::create('chatwoot_contacts', function (Blueprint $table) {
            $table->integer('id')->primary();  // Set as primary key, manually managed
            $table->string('name')->nullable()->default('');
            $table->string('email')->nullable();
            $table->string('phone_number')->nullable();
            $table->jsonb('additional_attributes')->nullable()->default('{}');
            $table->string('identifier')->nullable();
            $table->jsonb('custom_attributes')->nullable()->default('{}');
            $table->timestamp('last_activity_at')->nullable();
            $table->string('middle_name')->nullable()->default('');
            $table->string('last_name')->nullable()->default('');
            $table->string('location')->nullable()->default('');
            $table->string('country_code')->nullable()->default('');
            $table->boolean('blocked')->default(false);
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();        
            $table->timestamps();  // Automatically adds created_at and updated_at columns
            $table->softDeletes();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatwoot_contacts');
    }
};
