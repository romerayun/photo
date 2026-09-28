<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shoot;
use App\Models\ShootFile;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShootController extends Controller
{
    /**
     * Display a calendar and list of shoots.
     */
    public function index(Request $request): View|JsonResponse
    {
        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        if ($month < 1 || $month > 12) {
            $month = now()->month;
        }

        $currentMonthDate = Carbon::createFromDate($year, $month, 1);
        $startOfMonth = $currentMonthDate->copy()->startOfMonth();
        $endOfMonth = $currentMonthDate->copy()->endOfMonth();

        // Shoots for the requested month + margin
        $shootsInMonth = Shoot::with('files')
            ->whereBetween('shoot_date', [
                $startOfMonth->copy()->subDays(7)->toDateString(),
                $endOfMonth->copy()->addDays(7)->toDateString(),
            ])
            ->orderBy('shoot_date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();

        // If AJAX JSON request for calendar events
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'shoots' => $shootsInMonth->map(fn($s) => $this->formatShootForJson($s)),
            ]);
        }

        // Upcoming shoots for quick sidebar / list
        $upcomingShoots = Shoot::with('files')->upcoming()->take(10)->get();

        // All shoots for list view (paginated or latest)
        $statusFilter = $request->input('status');
        $query = Shoot::with('files')->orderBy('shoot_date', 'desc')->orderBy('start_time', 'desc');

        if ($statusFilter && in_array($statusFilter, ['planned', 'completed', 'cancelled'])) {
            $query->where('status', $statusFilter);
        }

        $allShoots = $query->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total' => Shoot::count(),
            'upcoming' => Shoot::upcoming()->count(),
            'completed' => Shoot::where('status', 'completed')->count(),
            'this_month' => Shoot::whereYear('shoot_date', $year)->whereMonth('shoot_date', $month)->count(),
        ];

        $prefillClient = null;
        if ($request->has('client_id')) {
            $prefillClient = \App\Models\Client::find($request->input('client_id'));
        }

        return view('admin.shoots.index', [
            'currentDate' => $currentMonthDate,
            'year' => $year,
            'month' => $month,
            'shootsInMonth' => $shootsInMonth,
            'upcomingShoots' => $upcomingShoots,
            'allShoots' => $allShoots,
            'stats' => $stats,
            'statusFilter' => $statusFilter,
            'prefillClient' => $prefillClient ? [
                'id' => $prefillClient->id,
                'name' => $prefillClient->name,
                'phone' => $prefillClient->phone,
                'social_link' => $prefillClient->social_link,
            ] : null,
        ]);
    }

    /**
     * Store a newly created shoot.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'client_name' => ['required', 'string', 'max:255'],
            'social_link' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'shoot_date' => ['required', 'date'],
            'start_time' => ['required', 'string', 'max:10'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:1440'],
            'status' => ['nullable', 'string', 'in:planned,completed,cancelled'],
            'location' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'integer', 'min:0'],
            'prepayment' => ['nullable', 'integer', 'min:0'],
            'gallery_link' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:51200'],
            'file_titles' => ['nullable', 'array'],
            'file_titles.*' => ['nullable', 'string', 'max:255'],
            'contract_files' => ['nullable', 'array'],
            'contract_files.*' => ['file', 'max:51200'],
            'contract_file_titles' => ['nullable', 'array'],
            'contract_file_titles.*' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['status'] = $validated['status'] ?? 'planned';
        $validated['start_time'] = substr($validated['start_time'], 0, 5);

        // Find or create Client
        $client = null;
        if (!empty($validated['client_id'])) {
            $client = \App\Models\Client::find($validated['client_id']);
        }
        if (!$client && !empty($validated['phone'])) {
            $client = \App\Models\Client::where('phone', $validated['phone'])->first();
        }
        if (!$client && !empty($validated['client_name'])) {
            $client = \App\Models\Client::where('name', $validated['client_name'])->first();
        }
        if (!$client && !empty($validated['client_name'])) {
            $client = \App\Models\Client::create([
                'name' => $validated['client_name'],
                'phone' => $validated['phone'] ?? null,
                'social_link' => $validated['social_link'] ?? null,
            ]);
        }

        if ($client) {
            $validated['client_id'] = $client->id;
            // Inherit client MAX credentials if present
            if ($client->max_connected_at) {
                $validated['max_user_id'] = $client->max_user_id;
                $validated['max_chat_id'] = $client->max_chat_id;
                $validated['max_connected_at'] = $client->max_connected_at;
            }
        }

        $shoot = Shoot::create($validated);

        if ($request->hasFile('files')) {
            $this->storeUploadedFiles($shoot, $request->file('files'), $request->input('file_titles', []), 'general');
        }

        if ($request->hasFile('contract_files')) {
            $this->storeUploadedFiles($shoot, $request->file('contract_files'), $request->input('contract_file_titles', []), 'contract');
        }

        $shoot->load('files');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Съёмка успешно добавлена в календарь!',
                'shoot' => $this->formatShootForJson($shoot),
            ]);
        }

        return redirect()->back()->with('success', "Съёмка для '{$shoot->client_name}' успешно добавлена!");
    }

    /**
     * Update an existing shoot.
     */
    public function update(Request $request, Shoot $shoot): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'client_name' => ['required', 'string', 'max:255'],
            'social_link' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'shoot_date' => ['required', 'date'],
            'start_time' => ['required', 'string', 'max:10'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:1440'],
            'status' => ['nullable', 'string', 'in:planned,completed,cancelled'],
            'location' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'integer', 'min:0'],
            'prepayment' => ['nullable', 'integer', 'min:0'],
            'gallery_link' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string'],
            'files' => ['nullable', 'array'],
            'files.*' => ['file', 'max:51200'],
            'file_titles' => ['nullable', 'array'],
            'file_titles.*' => ['nullable', 'string', 'max:255'],
            'contract_files' => ['nullable', 'array'],
            'contract_files.*' => ['file', 'max:51200'],
            'contract_file_titles' => ['nullable', 'array'],
            'contract_file_titles.*' => ['nullable', 'string', 'max:255'],
        ]);

        $validated['status'] = $validated['status'] ?? 'planned';
        $validated['start_time'] = substr($validated['start_time'], 0, 5);

        // Client association
        $client = null;
        if (!empty($validated['client_id'])) {
            $client = \App\Models\Client::find($validated['client_id']);
        }
        if (!$client && !empty($validated['phone'])) {
            $client = \App\Models\Client::where('phone', $validated['phone'])->first();
        }
        if (!$client && !empty($validated['client_name'])) {
            $client = \App\Models\Client::where('name', $validated['client_name'])->first();
        }
        if ($client) {
            $validated['client_id'] = $client->id;
            if ($client->max_connected_at && !$shoot->max_connected_at) {
                $validated['max_user_id'] = $client->max_user_id;
                $validated['max_chat_id'] = $client->max_chat_id;
                $validated['max_connected_at'] = $client->max_connected_at;
            }
        }

        $shoot->update($validated);

        if ($request->hasFile('files')) {
            $this->storeUploadedFiles($shoot, $request->file('files'), $request->input('file_titles', []), 'general');
        }

        if ($request->hasFile('contract_files')) {
            $this->storeUploadedFiles($shoot, $request->file('contract_files'), $request->input('contract_file_titles', []), 'contract');
        }

        $shoot->load('files');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Данные о съёмке успешно обновлены!',
                'shoot' => $this->formatShootForJson($shoot),
            ]);
        }

        return redirect()->back()->with('success', "Данные съёмки для '{$shoot->client_name}' обновлены.");
    }

    /**
     * Upload additional files to a shoot.
     */
    public function uploadFiles(Request $request, Shoot $shoot): JsonResponse
    {
        $request->validate([
            'files' => ['required', 'array'],
            'files.*' => ['required', 'file', 'max:51200'],
            'category' => ['nullable', 'string', 'in:general,contract'],
            'titles' => ['nullable', 'array'],
            'titles.*' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $category = $request->input('category', 'general');
        $titles = $request->input('titles', []);
        if (empty($titles) && $request->filled('title')) {
            $titles = array_fill(0, count($request->file('files')), $request->input('title'));
        }

        $newFiles = $this->storeUploadedFiles($shoot, $request->file('files'), $titles, $category);

        return response()->json([
            'success' => true,
            'message' => 'Файлы успешно загружены!',
            'files' => $newFiles->map(fn($f) => [
                'id' => $f->id,
                'original_name' => $f->original_name,
                'title' => $f->title,
                'display_name' => $f->display_name,
                'category' => $f->category,
                'url' => $f->url,
                'is_image' => $f->is_image,
                'formatted_size' => $f->formatted_size,
                'extension' => $f->extension,
                'created_at' => $f->created_at->format('d.m.Y H:i'),
            ]),
        ]);
    }

    public function updateFile(Request $request, ShootFile $file): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'in:general,contract'],
        ]);

        $file->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Информация о файле обновлена.',
            'file' => [
                'id' => $file->id,
                'original_name' => $file->original_name,
                'title' => $file->title,
                'display_name' => $file->display_name,
                'category' => $file->category,
                'url' => $file->url,
                'is_image' => $file->is_image,
                'formatted_size' => $file->formatted_size,
                'extension' => $file->extension,
                'created_at' => $file->created_at->format('d.m.Y H:i'),
            ],
        ]);
    }

    /**
     * Delete an attached file.
     */
    public function destroyFile(ShootFile $file): JsonResponse
    {
        $name = $file->original_name;
        $file->delete();

        return response()->json([
            'success' => true,
            'message' => "Файл '{$name}' удалён.",
        ]);
    }

    /**
     * Quick status update.
     */
    public function updateStatus(Request $request, Shoot $shoot): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:planned,completed,cancelled'],
        ]);

        $shoot->update(['status' => $validated['status']]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Статус съёмки изменён.',
                'status' => $shoot->status,
                'status_label' => $shoot->status_label,
            ]);
        }

        return redirect()->back()->with('success', "Статус съёмки изменён на '{$shoot->status_label}'.");
    }

    /**
     * Quick gallery link update.
     */
    public function updateGalleryLink(Request $request, Shoot $shoot): JsonResponse
    {
        $validated = $request->validate([
            'gallery_link' => ['nullable', 'string', 'max:2000'],
        ]);

        $link = !empty($validated['gallery_link']) ? trim($validated['gallery_link']) : null;
        $updates = ['gallery_link' => $link];

        if (!empty($link) && $shoot->status === 'planned') {
            $updates['status'] = 'completed';
        }

        $shoot->update($updates);

        return response()->json([
            'success' => true,
            'message' => 'Ссылка на готовые фотографии сохранена!',
            'gallery_link' => $shoot->gallery_link,
            'clean_gallery_url' => $shoot->clean_gallery_url,
            'status' => $shoot->status,
            'status_label' => $shoot->status_label,
        ]);
    }

    /**
     * Delete a shoot.
     */
    public function destroy(Request $request, Shoot $shoot): RedirectResponse|JsonResponse
    {
        $name = $shoot->client_name;
        $shoot->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Съёмка для '{$name}' удалена.",
            ]);
        }

        return redirect()->back()->with('success', "Съёмка для '{$name}' удалена из календаря.");
    }

    /**
     * Store multiple uploaded files for a shoot.
     */
    private function storeUploadedFiles(Shoot $shoot, array $files, array $titles = [], string $category = 'general')
    {
        $created = collect();

        foreach ($files as $i => $file) {
            $originalName = $file->getClientOriginalName();
            $path = $file->store("shoots/{$shoot->id}", 'public');
            $title = !empty($titles[$i]) ? trim($titles[$i]) : null;

            $shootFile = $shoot->files()->create([
                'file_path' => $path,
                'original_name' => $originalName,
                'title' => $title,
                'category' => $category,
                'mime_type' => $file->getClientMimeType(),
                'file_size' => $file->getSize(),
            ]);

            $created->push($shootFile);
        }

        return $created;
    }

    /**
     * Format a shoot model into a JSON-friendly array for frontend calendar.
     */
    private function formatShootForJson(Shoot $shoot): array
    {
        return [
            'id' => $shoot->id,
            'client_id' => $shoot->client_id,
            'client_name' => $shoot->client_name,
            'social_link' => $shoot->social_link,
            'social_url' => $shoot->social_url,
            'phone' => $shoot->phone,
            'phone_clean' => $shoot->phone_clean,
            'description' => $shoot->description,
            'shoot_date' => $shoot->shoot_date->format('Y-m-d'),
            'start_time' => substr($shoot->start_time, 0, 5),
            'end_time' => $shoot->end_time,
            'duration_minutes' => $shoot->duration_minutes,
            'duration_label' => $shoot->duration_label,
            'status' => $shoot->status,
            'status_label' => $shoot->status_label,
            'location' => $shoot->location,
            'price' => $shoot->price,
            'prepayment' => $shoot->prepayment,
            'prepayment_amount' => $shoot->prepayment_amount,
            'remainder_amount' => $shoot->remainder_amount,
            'contract_number' => $shoot->contract_number,
            'notes' => $shoot->notes,
            'gallery_link' => $shoot->gallery_link,
            'clean_gallery_url' => $shoot->clean_gallery_url,
            'share_url' => $shoot->share_url,
            'share_token' => $shoot->share_token,
            'receipt_url' => $shoot->receipt_url,
            'receipt_original_name' => $shoot->receipt_original_name,
            'receipt_uploaded_at' => $shoot->receipt_uploaded_at ? $shoot->receipt_uploaded_at->timezone('Asia/Irkutsk')->format('d.m.Y H:i') : null,
            'booking_confirmed_at' => $shoot->booking_confirmed_at ? $shoot->booking_confirmed_at->timezone('Asia/Irkutsk')->format('d.m.Y H:i') : null,
            'files' => $shoot->files->map(fn($f) => [
                'id' => $f->id,
                'original_name' => $f->original_name,
                'title' => $f->title,
                'display_name' => $f->display_name,
                'category' => $f->category ?? 'general',
                'url' => $f->url,
                'is_image' => $f->is_image,
                'formatted_size' => $f->formatted_size,
                'extension' => $f->extension,
                'created_at' => $f->created_at->format('d.m.Y H:i'),
            ])->values()->all(),
        ];
    }

    /**
     * Admin confirms booking after reviewing the client receipt.
     */
    public function confirmBooking(Shoot $shoot): \Illuminate\Http\JsonResponse
    {
        $shoot->update([
            'booking_confirmed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'booking_confirmed_at' => $shoot->booking_confirmed_at->timezone('Asia/Irkutsk')->format('d.m.Y H:i'),
            'shoot' => $this->formatShootForJson($shoot),
        ]);
    }
}
