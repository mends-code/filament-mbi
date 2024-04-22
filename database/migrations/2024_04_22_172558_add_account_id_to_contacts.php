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
        Schema::table('chatwoot_contacts', function (Blueprint $table) {
            $table->foreignId('chatwoot_account_id')->nullable()->constrained('chatwoot_accounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chatwoot_contacts', function (Blueprint $table) {
        });
    }
};
