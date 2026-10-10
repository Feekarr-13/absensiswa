<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')->constrained();
            $table->foreignId('subject_id')->constrained();
            $table->foreignId('teacher_id')->constrained();
            $table->unsignedTinyInteger('day_of_week'); // 1=Senin ... 7=Minggu
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room', 30);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['classroom_id', 'day_of_week']);
            $table->index(['teacher_id', 'day_of_week']);
        });

        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained();
            $table->foreignId('created_by')->constrained('users');
            $table->date('session_date');
            $table->string('status', 10)->default('draft')->index();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('late_after')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->char('qr_token_hash', 64)->nullable()->index();
            $table->char('previous_qr_token_hash', 64)->nullable();
            $table->dateTime('qr_rotated_at')->nullable();
            $table->unsignedSmallInteger('qr_rotation_seconds')->default(30);
            $table->timestamps();
            $table->unique(['schedule_id', 'session_date']);
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_session_id')->constrained();
            $table->foreignId('student_id')->constrained();
            $table->date('attendance_date'); // denormalisasi untuk query riwayat
            $table->string('status', 10);
            $table->dateTime('check_in_at')->nullable();
            $table->dateTime('check_out_at')->nullable();
            $table->string('source', 10)->default('qr'); // qr | manual | correction
            $table->text('note')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['attendance_session_id', 'student_id']);
            $table->index(['student_id', 'attendance_date']);
            $table->index(['student_id', 'status']);
            $table->index('attendance_date');
        });

        Schema::create('attendance_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained();
            $table->foreignId('requested_by')->constrained('users');
            $table->string('previous_status', 10);
            $table->string('requested_status', 10);
            $table->text('reason');
            $table->string('status', 10)->default('pending')->index(); // pending | approved | rejected
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->text('decision_note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['attendance_corrections', 'attendances', 'attendance_sessions', 'schedules'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};