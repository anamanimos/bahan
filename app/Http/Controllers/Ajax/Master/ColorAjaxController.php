<?php

namespace App\Http\Controllers\Ajax\Master;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ColorAjaxController extends Controller
{
    /**
     * Store a newly created color via AJAX.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|unique:colors,name',
            'hex_code' => 'nullable'
        ]);

        $color = Color::create([
            'name' => $request->name,
            'hex_code' => $request->hex_code
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Warna berhasil ditambahkan.',
            'data' => $color
        ]);
    }
}
