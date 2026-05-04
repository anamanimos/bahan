<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Webhook;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $webhooks = Webhook::latest()->get();
        return view('pages.admin.webhook.index', compact('webhooks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.admin.webhook.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'secret' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        Webhook::create($request->all());

        return redirect()->route('admin.webhook.index')
            ->with('success', 'Webhook berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Webhook $webhook)
    {
        return view('pages.admin.webhook.edit', compact('webhook'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Webhook $webhook)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
            'secret' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $webhook->update($request->all());

        return redirect()->route('admin.webhook.index')
            ->with('success', 'Webhook berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Webhook $webhook)
    {
        $webhook->delete();

        return response()->json([
            'success' => true,
            'message' => 'Webhook berhasil dihapus.'
        ]);
    }

    /**
     * Display the webhook documentation.
     */
    public function documentation()
    {
        return view('pages.admin.webhook.documentation');
    }
}
