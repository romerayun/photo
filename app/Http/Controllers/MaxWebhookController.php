<?php

namespace App\Http\Controllers;

use App\Models\Shoot;
use App\Services\MaxMessengerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MaxWebhookController extends Controller
{
    /**
     * Handle incoming webhook from MAX messenger platform.
     * Expected event: bot_started (or message_created with payload / start parameter).
     */
    public function handle(Request $request, MaxMessengerService $maxService): JsonResponse
    {
        Log::info('MAX Webhook received payload:', $request->all());

        // Extract event data according to MAX Bot API formats
        $event = $request->input('event') ?? $request->input('type');
        
        // 1. Extract payload code
        // Various possible structures in bot platforms:
        // - $request->input('payload')
        // - $request->input('data.payload')
        // - $request->input('message.text') (e.g. "/start <code>")
        $payload = $request->input('payload') 
            ?? $request->input('data.payload')
            ?? $request->input('data.start_payload')
            ?? $request->input('message.payload');

        // Check if code was passed in text like "/start <code>"
        if (!$payload) {
            $text = $request->input('message.text') ?? $request->input('text') ?? '';
            if (preg_match('/^\/start\s+([a-zA-Z0-9_-]+)/', trim($text), $matches)) {
                $payload = $matches[1];
            }
        }

        // 2. Extract user_id and chat_id
        $userId = $request->input('user.user_id') 
            ?? $request->input('user_id') 
            ?? $request->input('data.user.user_id') 
            ?? $request->input('message.from.id')
            ?? $request->input('sender.id');

        $chatId = $request->input('chat_id') 
            ?? $request->input('data.chat_id') 
            ?? $request->input('message.chat.id')
            ?? $request->input('chat.id')
            ?? $userId;

        if (!$payload) {
            return response()->json([
                'status' => 'ignored',
                'message' => 'No start payload found in event',
            ], 200);
        }

        $codeHash = hash('sha256', trim($payload));

        // 3. Atomically find shoot, check expiration, redeem code, and link MAX user
        $shoot = DB::transaction(function () use ($codeHash, $userId, $chatId) {
            $shootRecord = Shoot::where('max_link_code_hash', $codeHash)
                ->where('max_link_code_expires_at', '>=', now())
                ->lockForUpdate()
                ->first();

            if (!$shootRecord) {
                return null;
            }

            // Atomically invalidate code and save MAX identifiers
            $shootRecord->update([
                'max_link_code_hash' => null,
                'max_link_code_expires_at' => null,
                'max_user_id' => $userId ? (string)$userId : null,
                'max_chat_id' => $chatId ? (string)$chatId : null,
                'max_connected_at' => now(),
            ]);

            return $shootRecord;
        });

        if (!$shoot) {
            Log::warning("MAX Webhook: invalid or expired start code '{$payload}'");
            return response()->json([
                'status' => 'error',
                'message' => 'Code invalid or expired',
            ], 400);
        }

        // 4. Send confirmation message
        $maxService->sendConfirmation($shoot);

        Log::info("MAX linked successfully to shoot ID #{$shoot->id} for user {$userId}");

        return response()->json([
            'status' => 'ok',
            'shoot_id' => $shoot->id,
            'message' => 'Shoot successfully linked to MAX and confirmation sent',
        ]);
    }
}
