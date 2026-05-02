<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminClaimController extends Controller
{
    public function claim(Request $request)
    {
        $request->validate([
            'secret_code' => 'required|string',
        ]);

        if ($request->secret_code === config('app.admin_secret_code')) {
            $user = auth()->user();
            $user->assignRole('admin');
            return redirect('/dashboard')->with('success', 'Berhasil claim akses Admin!');
        }

        return redirect()->back()->with('error', 'Kode rahasia salah.');
    }
}
