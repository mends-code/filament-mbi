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
        // Drop existing tables
        Schema::dropIfExists('mbi_filament.users');
        Schema::dropIfExists('mbi_filament.password_reset_tokens');
        Schema::dropIfExists('mbi_filament.sessions');
        Schema::dropIfExists('mbi_filament.cache');
        Schema::dropIfExists('mbi_filament.cache_locks');
        Schema::dropIfExists('mbi_filament.jobs');
        Schema::dropIfExists('mbi_filament.job_batches');
        Schema::dropIfExists('mbi_filament.failed_jobs');
        Schema::dropIfExists('socialite_users');  // Drop the socialite_users table

        // Recreate tables
        Schema::create('mbi_filament.users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('mbi_filament.password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('mbi_filament.sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        Schema::create('mbi_filament.cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('mbi_filament.cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        Schema::create('mbi_filament.jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('mbi_filament.job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('mbi_filament.failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mbi_filament.users');
        Schema::dropIfExists('mbi_filament.password_reset_tokens');
        Schema::dropIfExists('mbi_filament.sessions');
        Schema::dropIfExists('mbi_filament.cache');
        Schema::dropIfExists('mbi_filament.cache_locks');
        Schema::dropIfExists('mbi_filament.jobs');
        Schema::dropIfExists('mbi_filament.job_batches');
        Schema::dropIfExists('mbi_filament.failed_jobs');
        Schema::dropIfExists('socialite_users');
    }
};
