<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Definisikan Data Master Satuan
        $units = [
            [
                'name' => 'Meter',
                'symbol' => 'Mtr',
                'description' => 'Satuan panjang standar'
            ],
            [
                'name' => 'Kilogram',
                'symbol' => 'Kg',
                'description' => 'Satuan berat standar'
            ],
            [
                'name' => 'Roll',
                'symbol' => 'Roll',
                'description' => 'Satuan gulungan bahan'
            ],
            [
                'name' => 'Pieces',
                'symbol' => 'Pcs',
                'description' => 'Satuan bijian/item'
            ],
            [
                'name' => 'Yard',
                'symbol' => 'Yrd',
                'description' => 'Satuan panjang yard'
            ],
        ];

        foreach ($units as $unitData) {
            Unit::updateOrCreate(
                ['symbol' => $unitData['symbol']],
                $unitData
            );
        }

        // 2. Normalisasi Data Produk Lama
        // Mengubah variasi penulisan lama ke standar simbol baru
        $normalizationMap = [
            'kg' => 'Kg',
            'KG' => 'Kg',
            'm' => 'Mtr',
            'meter' => 'Mtr',
            'Meter' => 'Mtr',
            'pcs' => 'Pcs',
            'PCS' => 'Pcs',
            'roll' => 'Roll',
            'ROLL' => 'Roll',
        ];

        foreach ($normalizationMap as $old => $new) {
            Product::where('base_unit', $old)->update(['base_unit' => $new]);
            
            // Juga update di transaksi jika perlu
            DB::table('purchase_requisition_items')->where('unit', $old)->update(['unit' => $new]);
            DB::table('goods_receipt_items')->where('unit', $old)->update(['unit' => $new]);
        }

        $this->command->info('Unit Seeder: Master data populated and existing products normalized!');
    }
}
