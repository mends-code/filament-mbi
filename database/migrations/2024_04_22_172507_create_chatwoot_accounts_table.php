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
        Schema::create('chatwoot_accounts', function (Blueprint $table) {
            $table->integer('id')->primary();  // Set as primary key, manually managed
            $table->timestamps(); // Created_at and updated_at
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();        
            $table->string('name');
            $table->string('domain')->nullable();
            $table->string('support_email')->nullable();
            $table->jsonb('custom_attributes')->nullable()->default('{}');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatwoot_accounts');
    }
};
