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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('notify_task_assigned_email')->default(true)->after('email');
            $table->boolean('notify_task_reminder_email')->default(true)->after('notify_task_assigned_email');
            $table->boolean('notify_sprint_reminder_email')->default(true)->after('notify_task_reminder_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'notify_task_assigned_email',
                'notify_task_reminder_email',
                'notify_sprint_reminder_email',
            ]);
        });
    }
};
