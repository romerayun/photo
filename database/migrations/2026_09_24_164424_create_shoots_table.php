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
        Schema::create('shoots', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('social_link')->nullable();
            $table->string('phone')->nullable();
            $table->text('description')->nullable();
            $table->date('shoot_date');
            $table->string('start_time', 10)->default('12:00');
            $table->unsignedInteger('duration_minutes')->default(60);
            $table->string('status', 30)->default('planned'); // planned, completed, cancelled
            $table->string('location')->nullable();
            $table->unsignedInteger('price')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['shoot_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shoots');
    }
};
