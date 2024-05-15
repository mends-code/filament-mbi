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
        Schema::table('mbi_filament.users', function (Blueprint $table) {
            $table->string('id')->index()->change();
            $table->string('name')->index()->change();
            $table->string('email')->index()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mbi_filament.users', function (Blueprint $table) {
            $table->dropIndex(['id', 'name', 'email']);
        });
    }
};
