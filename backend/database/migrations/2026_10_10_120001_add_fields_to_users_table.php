<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('role', 20)->default('student')->index()->after('password');
            $table->string('phone', 20)->nullable();
            $table->string('avatar_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('school_id')->nullable()->index();
            $table->timestamp('last_login_at')->nullable();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropIndex(['role']);
            $table->dropIndex(['school_id']);
            $table->dropSoftDeletes();
            $table->dropColumn(['username', 'role', 'phone', 'avatar_path', 'is_active', 'school_id', 'last_login_at']);
        });
    }
};