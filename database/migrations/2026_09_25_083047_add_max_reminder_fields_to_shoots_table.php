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
        Schema::table('shoots', function (Blueprint $table) {
            $table->string('max_link_code_hash', 64)->nullable()->index();
            $table->timestamp('max_link_code_expires_at')->nullable();
            $table->string('max_user_id')->nullable()->index();
            $table->string('max_chat_id')->nullable();
            $table->timestamp('max_connected_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shoots', function (Blueprint $table) {
            $table->dropIndex(['max_link_code_hash']);
            $table->dropIndex(['max_user_id']);
            $table->dropColumn([
                'max_link_code_hash',
                'max_link_code_expires_at',
                'max_user_id',
                'max_chat_id',
                'max_connected_at',
            ]);
        });
    }
};
