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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // المالك للمشروع
            $table->string('name');
            $table->string('category')->default('General'); // the default value is General
            $table->string('expected_duration')->nullable(); // 1_month, 3_months, etc.
            $table->text('description')->nullable();
            $table->boolean('use_ai_scaffold')->default(false); // هل طلب تفكيك بالذكاء الاصطناعي؟
            $table->string('status')->default('active'); // active, archived, completed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
