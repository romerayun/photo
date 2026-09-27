<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable()->index();
            $table->string('social_link')->nullable();
            $table->string('email')->nullable();
            $table->text('notes')->nullable();

            // MAX messenger credentials
            $table->string('max_user_id')->nullable()->index();
            $table->string('max_chat_id')->nullable()->index();
            $table->timestamp('max_connected_at')->nullable();

            $table->timestamps();
        });

        // Add client_id foreign key to shoots table
        Schema::table('shoots', function (Blueprint $table) {
            $table->foreignId('client_id')->nullable()->after('id')->constrained('clients')->nullOnDelete();
        });

        // Backfill existing clients from shoots table
        $existingShoots = DB::table('shoots')->orderBy('id')->get();
        foreach ($existingShoots as $shoot) {
            $clientName = trim($shoot->client_name ?? '');
            if (!$clientName) {
                continue;
            }

            // Look for existing client with same phone (if non-empty) or same name
            $query = DB::table('clients');
            if (!empty($shoot->phone)) {
                $client = $query->where('phone', $shoot->phone)->first();
            } else {
                $client = $query->where('name', $clientName)->first();
            }

            if (!$client) {
                $clientId = DB::table('clients')->insertGetId([
                    'name' => $clientName,
                    'phone' => $shoot->phone,
                    'social_link' => $shoot->social_link,
                    'max_user_id' => $shoot->max_user_id,
                    'max_chat_id' => $shoot->max_chat_id,
                    'max_connected_at' => $shoot->max_connected_at,
                    'created_at' => $shoot->created_at ?? now(),
                    'updated_at' => $shoot->updated_at ?? now(),
                ]);
            } else {
                $clientId = $client->id;
                // If shoot had MAX credentials but client didn't, backfill to client
                $updates = [];
                if (empty($client->max_user_id) && !empty($shoot->max_user_id)) {
                    $updates['max_user_id'] = $shoot->max_user_id;
                }
                if (empty($client->max_chat_id) && !empty($shoot->max_chat_id)) {
                    $updates['max_chat_id'] = $shoot->max_chat_id;
                }
                if (empty($client->max_connected_at) && !empty($shoot->max_connected_at)) {
                    $updates['max_connected_at'] = $shoot->max_connected_at;
                }
                if (!empty($updates)) {
                    $updates['updated_at'] = now();
                    DB::table('clients')->where('id', $clientId)->update($updates);
                }
            }

            DB::table('shoots')->where('id', $shoot->id)->update(['client_id' => $clientId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shoots', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn('client_id');
        });

        Schema::dropIfExists('clients');
    }
};
