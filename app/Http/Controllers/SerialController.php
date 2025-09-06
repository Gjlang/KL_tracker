<?php

// app/Http/Controllers/SerialController.php
namespace App\Http\Controllers;


use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\MasterFile;   // optional kalau mau hitung sequence per hari

class SerialController extends Controller
{
    public function preview(Request $r)
    {
        try {
            $dateStr = (string) $r->query('date', '');
            $product = (string) $r->query('product', '');

            // Parse tanggal fleksibel
            $date = \DateTimeImmutable::createFromFormat('Y-m-d', $dateStr)
                 ?: (new \DateTimeImmutable($dateStr ?: 'now'));

            // Slug produk: huruf/angka saja
            $slug = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $product)) ?: 'GEN';

            // (Opsional) sequence berdasarkan data existing hari itu
            // $count = MasterFile::whereDate('date', $date->format('Y-m-d'))
            //            ->where('product', $product)->count();
            // $seq   = str_pad($count + 1, 3, '0', STR_PAD_LEFT);

            // Simple fixed seq jika belum pakai DB count
            $seq = '001';

            $job = $date->format('Ymd') . '-' . $slug . '-' . $seq;

            return response()->json(['job_number' => $job]);
        } catch (\Throwable $e) {
            Log::error('serials.preview failed', ['msg' => $e->getMessage()]);
            // Jangan 500: tetap 200 supaya UI tidak rusak
            return response()->json(['job_number' => null]);
        }
    }
}
