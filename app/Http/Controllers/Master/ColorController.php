<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    /**
     * Display a listing of colors.
     */
    public function index()
    {
        return view('pages.master.color.index');
    }

    /**
     * Export colors to CSV.
     */
    public function exportCsv()
    {
        $colors = Color::all();
        $filename = "colors_export_" . date('Ymd_His') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Name', 'Hex Code'];

        $callback = function() use($colors, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($colors as $color) {
                fputcsv($file, [$color->name, $color->hex_code]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download CSV template for import.
     */
    public function downloadTemplate()
    {
        $filename = "color_import_template.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Name', 'Hex Code'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['Hitam Reaktif', '#000000']);
            fputcsv($file, ['Putih Bersih', '#FFFFFF']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
