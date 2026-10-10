<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_holidays', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name');
            $table->text('address')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('timezone', 40)->default('Asia/Jakarta');
            $table->unsignedSmallInteger('late_after_minutes')->default(10);
            $table->unsignedSmallInteger('qr_rotation_seconds')->default(30);
            $table->time('operating_start')->default('07:00:00');
            $table->time('operating_end')->default('16:00:00');
            $table->timestamps();
        });

        Schema::create('user_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->boolean('attendance')->default(true);
            $table->boolean('schedule_reminder')->default(true);
            $table->boolean('announcement')->default(true);
            $table->boolean('correction')->default(true);
            $table->boolean('email_enabled')->default(false);
            $table->string('language', 5)->default('id');
            $table->boolean('dark_mode')->default(false);
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 50);
            $table->nullableMorphs('auditable');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->text('reason')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index('action');
        });
    }

    public function down(): void
    {
        foreach (['audit_logs', 'user_notification_preferences', 'school_settings', 'school_holidays'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};