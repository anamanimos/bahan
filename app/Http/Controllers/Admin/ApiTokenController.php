<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    public function index()
    {
        $tokens = auth()->user()->tokens;
        return view('admin.api.token', compact('tokens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $token = auth()->user()->createToken($request->name);

        return back()->with('success', 'API Token created successfully.')->with('plainTextToken', $token->plainTextToken);
    }

    public function destroy($id)
    {
        auth()->user()->tokens()->where('id', $id)->delete();
        return back()->with('success', 'API Token revoked successfully.');
    }
}
