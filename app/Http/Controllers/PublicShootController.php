<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Shoot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PublicShootController extends Controller
{
    /**
     * Show the public client page for a shoot.
     */
    public function show(string $token): View
    {
        $shoot = Shoot::where('share_token', $token)
            ->with('files')
            ->firstOrFail();

        $photographerPhone = Setting::get('phone', '+7 (999) 000-00-00');
        $photographerTelegram = Setting::telegramUrl("Здравствуйте, Роман! Пишу по поводу фотосессии для {$shoot->client_name}.");
        $photographerEmail = Setting::get('email', 'contact@romanyun.ru');

        return view('pages.shoots.show', [
            'shoot' => $shoot,
            'photographerPhone' => $photographerPhone,
            'photographerTelegram' => $photographerTelegram,
            'photographerEmail' => $photographerEmail,
        ]);
    }

    /**
     * Download iCal (.ics) file for the shoot.
     */
    public function ics(string $token): Response
    {
        $shoot = Shoot::where('share_token', $token)->firstOrFail();

        $dateStr = $shoot->shoot_date->format('Y-m-d');
        $startTime = substr($shoot->start_time, 0, 5);
        $durationMinutes = $shoot->duration_minutes ?? 60;

        try {
            $startDt = Carbon::createFromFormat('Y-m-d H:i', "{$dateStr} {$startTime}");
            $endDt = $startDt->copy()->addMinutes($durationMinutes);
        } catch (\Exception $e) {
            $startDt = Carbon::parse($dateStr);
            $endDt = $startDt->copy()->addHour();
        }

        $dtStart = $startDt->utc()->format('Ymd\THis\Z');
        $dtEnd = $endDt->utc()->format('Ymd\THis\Z');
        $dtStamp = now()->utc()->format('Ymd\THis\Z');
        $uid = "shoot-{$shoot->id}-" . md5($shoot->share_token) . "@romanyun.ru";

        $summary = "Фотосессия с Романом Юном ({$shoot->client_name})";
        $location = $shoot->location ? addcslashes($shoot->location, ",;") : "г. Москва";
        $description = addcslashes($shoot->description ?: "Фотосессия. Детали по ссылке: " . route('shoots.share', ['token' => $shoot->share_token]), ",;\n");

        $ics = "BEGIN:VCALENDAR\r\n"
             . "VERSION:2.0\r\n"
             . "PRODID:-//Roman Yun//Photoshoot Booking//RU\r\n"
             . "CALSCALE:GREGORIAN\r\n"
             . "METHOD:PUBLISH\r\n"
             . "BEGIN:VEVENT\r\n"
             . "UID:{$uid}\r\n"
             . "DTSTAMP:{$dtStamp}\r\n"
             . "DTSTART:{$dtStart}\r\n"
             . "DTEND:{$dtEnd}\r\n"
             . "SUMMARY:{$summary}\r\n"
             . "DESCRIPTION:{$description}\r\n"
             . "LOCATION:{$location}\r\n"
             . "STATUS:CONFIRMED\r\n"
             . "END:VEVENT\r\n"
             . "END:VCALENDAR\r\n";

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"shoot-{$shoot->shoot_date->format('Y-m-d')}.ics\"",
        ]);
    }

    /**
     * Handle client receipt upload from the public share page.
     */
    public function uploadReceipt(Request $request, string $token): \Illuminate\Http\JsonResponse
    {
        $shoot = Shoot::where('share_token', $token)->firstOrFail();

        $request->validate([
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,heic,pdf', 'max:10240'],
        ]);

        $file = $request->file('receipt');
        $path = $file->store("shoots/{$shoot->id}/receipts", 'public');

        // Delete old receipt if exists
        if ($shoot->receipt_path && Storage::disk('public')->exists($shoot->receipt_path)) {
            Storage::disk('public')->delete($shoot->receipt_path);
        }

        $shoot->update([
            'receipt_path' => $path,
            'receipt_original_name' => $file->getClientOriginalName(),
            'receipt_uploaded_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'receipt_url' => '/storage/' . $path,
            'original_name' => $file->getClientOriginalName(),
            'uploaded_at' => now()->timezone('Asia/Irkutsk')->translatedFormat('d F Y, H:i'),
        ]);
    }

    /**
     * Generate one-time cryptographic code and deep link for connecting MAX messenger.
     */
    public function generateMaxLink(string $token, \App\Services\MaxMessengerService $maxService): \Illuminate\Http\JsonResponse
    {
        $shoot = Shoot::where('share_token', $token)->firstOrFail();

        // Generate cryptographically secure random token (32 chars)
        $plainCode = \Illuminate\Support\Str::random(32);
        $codeHash = hash('sha256', $plainCode);

        // Store hash and expiration (15 minutes)
        $shoot->update([
            'max_link_code_hash' => $codeHash,
            'max_link_code_expires_at' => now()->addMinutes(15),
        ]);

        $deepLink = $maxService->getStartLink($plainCode);

        return response()->json([
            'success' => true,
            'deep_link' => $deepLink,
            'expires_in_minutes' => 15,
            'bot_username' => $maxService->getBotUsername(),
        ]);
    }
}

