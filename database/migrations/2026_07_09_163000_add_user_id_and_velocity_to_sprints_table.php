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
        Schema::table('sprints', function (Blueprint $table) {
            // Check if column doesn't exist before adding to prevent errors
            if (!Schema::hasColumn('sprints', 'user_id')) {
                // If there are existing records, assign them to the first user or make it nullable first
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            }

            if (!Schema::hasColumn('sprints', 'target_velocity')) {
                $table->integer('target_velocity')->default(40)->after('status');
            }

            // Make project_id nullable if it isn't already
            $table->foreignId('project_id')->nullable()->change();

            // Make goal a text field
            $table->text('goal')->nullable()->change();
        });

        // If user_id is nullable (to prevent constraint issues with existing rows), make it non-nullable now
        // But to be safe, we can just default it to user ID 1 or leave it as nullable/constrained.
        // Let's set a default user_id for any existing sprints if needed:
        try {
            \Illuminate\Support\Facades\DB::table('sprints')
                ->whereNull('user_id')
                ->update(['user_id' => \Illuminate\Support\Facades\DB::table('users')->first()?->id ?? 1]);
        } catch (\Exception $e) {
            // Ignore if users table is empty or doesn't exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sprints', function (Blueprint $table) {
            if (Schema::hasColumn('sprints', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }

            if (Schema::hasColumn('sprints', 'target_velocity')) {
                $table->dropColumn('target_velocity');
            }
        });
    }
};
