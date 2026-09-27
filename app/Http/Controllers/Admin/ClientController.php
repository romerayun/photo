<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Shoot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * Display a listing of clients.
     */
    public function index(Request $request): View|JsonResponse
    {
        $search = trim($request->input('search', ''));

        $query = Client::with(['shoots' => function ($q) {
            $q->orderBy('shoot_date', 'desc');
        }])->withCount('shoots');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('social_link', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $clients = $query->orderBy('name', 'asc')->paginate(25)->withQueryString();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'clients' => $clients->items(),
            ]);
        }

        return view('admin.clients.index', compact('clients', 'search'));
    }

    /**
     * Quick search clients for autocomplete in calendar/shoot creation form.
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));
        if (mb_strlen($q) < 1) {
            $clients = Client::orderBy('name', 'asc')->take(15)->get();
        } else {
            $clients = Client::where('name', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%")
                ->orWhere('social_link', 'like', "%{$q}%")
                ->orderBy('name', 'asc')
                ->take(20)
                ->get();
        }

        return response()->json([
            'clients' => $clients->map(fn($c) => [
                'id' => $c->id,
                'name' => $c->name,
                'phone' => $c->phone,
                'social_link' => $c->social_link,
                'max_user_id' => $c->max_user_id,
                'max_chat_id' => $c->max_chat_id,
                'shoots_count' => $c->shoots()->count(),
            ]),
        ]);
    }

    /**
     * Store a newly created client.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'social_link' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $client = Client::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Клиент {$client->name} добавлен!",
                'client' => $client,
            ]);
        }

        return redirect()->route('admin.clients.index')->with('success', "Клиент '{$client->name}' успешно добавлен!");
    }

    /**
     * Show client details and all their shoots.
     */
    public function show(Client $client): View
    {
        $client->load(['shoots' => function ($q) {
            $q->orderBy('shoot_date', 'desc')->orderBy('start_time', 'desc');
        }]);

        return view('admin.clients.show', compact('client'));
    }

    /**
     * Update client information.
     */
    public function update(Request $request, Client $client): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'social_link' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $client->update($validated);

        // Also update client_name, phone, social_link on upcoming/linked shoots
        $client->shoots()->update([
            'client_name' => $client->name,
            'phone' => $client->phone,
            'social_link' => $client->social_link,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Данные клиента обновлены!',
                'client' => $client,
            ]);
        }

        return redirect()->back()->with('success', 'Данные клиента успешно обновлены!');
    }

    /**
     * Delete client and detach shoots.
     */
    public function destroy(Client $client): RedirectResponse
    {
        $name = $client->name;
        $client->delete();

        return redirect()->route('admin.clients.index')->with('success', "Клиент '{$name}' удален.");
    }
}
