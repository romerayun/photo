<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shoot_files', function (Blueprint $table) {
            $table->string('title')->nullable()->after('original_name');
            $table->string('category')->default('general')->after('title');
        });
    }

    public function down(): void
    {
        Schema::table('shoot_files', function (Blueprint $table) {
            $table->dropColumn(['title', 'category']);
        });
    }
};
