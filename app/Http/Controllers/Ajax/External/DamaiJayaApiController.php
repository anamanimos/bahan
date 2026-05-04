<?php

namespace App\Http\Controllers\Ajax\External;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DamaiJayaApiController extends Controller
{
    /**
     * Search orders from DamaiJaya API.
     */
    public function searchOrders(Request $request)
    {
        $search = $request->query('q');
        $apiUrl = config('services.damaijaya.url') . '/orders';
        $token = config('services.damaijaya.token');

        try {
            $response = Http::withHeaders([
                'x-api-key' => $token
            ])->get($apiUrl, [
                'search' => $search,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $orders = isset($data['data']) ? $data['data'] : [];

                $formattedOrders = array_map(function ($order) {
                    return [
                        'id' => $order['order_number'] ?? ($order['uuid'] ?? ($order['id'] ?? '')),
                        'text' => ($order['order_number'] ?? '') . ' - ' . ($order['order_name'] ?? ($order['name'] ?? 'Untitled')),
                    ];
                }, $orders);

                return response()->json([
                    'results' => $formattedOrders
                ]);
            }

            return response()->json([
                'error' => 'Failed to fetch data from external API'
            ], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
