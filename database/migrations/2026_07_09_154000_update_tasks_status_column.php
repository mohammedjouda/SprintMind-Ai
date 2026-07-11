<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            // First, update any existing 'pending' status tasks to 'todo'
            DB::table('tasks')->where('status', 'pending')->update(['status' => 'todo']);
        } catch (\Exception $e) {
            // Ignore if table does not exist or column is different
        }

        Schema::table('tasks', function (Blueprint $table) {
            $table->string('status')->default('todo')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }
};
