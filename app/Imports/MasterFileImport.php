<?php

namespace App\Imports;

use App\Models\MasterFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as XlsDate;

class MasterFileImport
{
    public static function run(UploadedFile $file): array
    {
        // ==== Read rows from file (no Laravel-Excel facade) ====
        $rows = self::readRowsWithHeaders($file->getRealPath());

        // ==== Table columns guard ====
        $columns = Schema::getColumnListing((new MasterFile)->getTable());
        $has     = fn($name) => in_array($name, $columns, true);

        // ==== Dynamic targets ====
        $jobTarget = $has('job') ? 'job' : ($has('job_status') ? 'job_status' : ($has('remarks') ? 'remarks' : null));
        $artTarget = $has('artwork') ? 'artwork' : ($has('artwork_status') ? 'artwork_status' : null);

        // ==== Natural key for upsert ====
        $naturalKey = array_values(array_filter([
            $has('company') ? 'company' : null,
            $has('client') ? 'client' : null,
            $has('product') ? 'product' : null,
            $has('month') ? 'month' : null,
            $has('date') ? 'date' : null,
        ]));

        $created = 0; $updated = 0; $skipped = 0;

        foreach ($rows as $row) {
            if (!is_array($row) || empty(array_filter($row, fn($v) => $v !== null && $v !== ''))) {
                $skipped++; continue;
            }

            // Normalize headers: "Date Created" => "date_created"
            $r = self::slugKeysUnderscore($row);

            // Fields
            $company = self::text($r['company_name'] ?? $r['company'] ?? null);
            $client  = self::text($r['client'] ?? null);
            $product = self::text($r['product'] ?? null);
            $status  = self::text($r['status'] ?? null);
            $traffic = self::text($r['traffic'] ?? null);
            $jobIn   = self::text($r['job'] ?? $r['job_status'] ?? null);
            $artIn   = self::text($r['artwork'] ?? $r['artwork_status'] ?? null);
            $invNo   = self::text($r['invoice_number'] ?? $r['invoice_no'] ?? null);
            $loc     = self::text($r['location'] ?? null);
            $category= self::inferCategory($product, $r['product_category'] ?? null);

            $createdAt = self::parseDate($r['date_created'] ?? $r['created_at'] ?? null);
            $startDate = self::parseDate($r['start_date']   ?? $r['date'] ?? null);
            $endDate   = self::parseDate($r['end_date']     ?? $r['date_finish'] ?? null);
            $invDate   = self::parseDate($r['invoice_date'] ?? $r['invoice_at'] ?? null);
            $rawMonth  = $r['month'] ?? null;
            $month     = self::normalizeMonth($rawMonth, $startDate);

            if ($startDate === null && $has('date')) {
                $startDate = self::getFallbackDate($createdAt, $rawMonth, $endDate);
            }

            $duration  = $r['duration'] ?? null;
            if ($duration === null && $startDate && $endDate) {
                try { $duration = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)); } catch (\Throwable $e) {}
            }

            // Payload (only existing columns + non-null values)
            $payload = [];
            if ($has('company') && $company !== null)                $payload['company'] = $company;
            if ($has('client') && $client !== null)                  $payload['client'] = $client;
            if ($has('product') && $product !== null)                $payload['product'] = $product;
            if ($has('product_category') && $category !== null)      $payload['product_category'] = $category;
            if ($has('month') && $month !== null)                    $payload['month'] = $month;
            if ($has('location') && $loc !== null)                   $payload['location'] = $loc;
            if ($has('status') && $status !== null)                  $payload['status'] = $status;
            if ($has('traffic') && $traffic !== null)                $payload['traffic'] = $traffic;
            if ($has('date'))                                        $payload['date'] = $startDate;
            if ($has('date_finish') && $endDate !== null)            $payload['date_finish'] = $endDate;
            if ($has('invoice_date') && $invDate !== null)           $payload['invoice_date'] = $invDate;
            if ($has('invoice_number') && $invNo !== null)           $payload['invoice_number'] = $invNo;
            if ($has('duration') && is_numeric($duration))           $payload['duration'] = (int)$duration;

            if ($jobTarget && $jobIn !== null)                       $payload[$jobTarget] = $jobIn;
            if ($artTarget && $artIn !== null)                       $payload[$artTarget] = $artIn;

            if ($has('created_at'))                                  $payload['created_at'] = $createdAt ?: now();
            if ($has('updated_at'))                                  $payload['updated_at'] = now();

            // Upsert by natural key
            $key  = array_intersect_key($payload, array_flip($naturalKey));
            $attr = array_diff_key($payload, array_flip($naturalKey));

            if (empty($key)) {
                MasterFile::create($payload);
                $created++;
            } else {
                $exists = MasterFile::where($key)->first();
                MasterFile::updateOrCreate($key, $attr);
                $exists ? $updated++ : $created++;
            }
        }

        return compact('created', 'updated', 'skipped');
    }

    /**
     * Read first sheet and return rows as associative arrays using the first row as header.
     */
    private static function readRowsWithHeaders(string $path): array
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheet(0);

        // Raw rows with column letters (A,B,C,...) preserved
        $raw = $sheet->toArray(null, true, true, true);
        if (empty($raw)) return [];

        // First row is assumed to be header
        $headersRow = array_shift($raw);
        // Normalize headers now; fallback to column letter if header cell is empty
        $headers = [];
        foreach ($headersRow as $col => $title) {
            $title = is_string($title) ? trim($title) : $title;
            $headers[$col] = $title !== null && $title !== '' ? $title : $col;
        }

        $rows = [];
        foreach ($raw as $row) {
            // Build associative row by header titles
            $assoc = [];
            foreach ($row as $col => $val) {
                $header = $headers[$col] ?? $col;
                $assoc[$header] = $val;
            }
            $rows[] = $assoc;
        }

        return $rows;
    }

    /** Ubah semua key array menjadi slug dengan underscore (contoh: "Date Created" => "date_created") */
    private static function slugKeysUnderscore(array $row): array
    {
        $out = [];
        foreach ($row as $k => $v) {
            $slug = Str::slug(trim((string)$k), '_');
            $slug = preg_replace('/_+/', '_', $slug);
            $out[$slug] = $v;
        }
        return $out;
    }

    private static function text($v) { return $v === null ? null : trim((string)$v); }

    /** Parser tanggal kompatibel Excel serial & string biasa (PhpSpreadsheet). */
    private static function parseDate($v)
    {
        if ($v === null || $v === '') return null;

        // Numeric Excel serial number
        if (is_numeric($v)) {
            try {
                $dt = XlsDate::excelToDateTimeObject((float)$v);
                return Carbon::instance($dt)->format('Y-m-d');
            } catch (\Throwable $e) { /* fallthrough */ }
        }

        // String date
        try { return Carbon::parse($v)->format('Y-m-d'); }
        catch (\Throwable $e) { return null; }
    }

    private static function normalizeMonth($m, $fallback = null)
    {
        if ($m === null || $m === '') {
            return $fallback ? Carbon::parse($fallback)->format('F') : null;
        }
        if (is_numeric($m)) {
            return Carbon::create(null, (int)$m, 1)->format('F');
        }
        try { return Carbon::parse('1 '.$m)->format('F'); }
        catch (\Throwable $e) { return (string)$m; }
    }

    private static function inferCategory($product, $explicit)
    {
        $exp = strtolower((string)$explicit);
        if ($exp) return ucfirst($exp);
        $p = strtolower((string)$product);
        if (strpos($p, 'kltg') !== false) return 'KLTG';
        if (strpos($p, 'fb') !== false || strpos($p, 'ig') !== false || strpos($p, 'media') !== false) return 'Media';
        return 'Outdoor';
    }

    private static function getFallbackDate($createdAt, $rawMonth, $endDate)
    {
        if ($createdAt) return $createdAt;

        if ($rawMonth) {
            try {
                $year = now()->year;
                if ($endDate) $year = Carbon::parse($endDate)->year;

                if (is_numeric($rawMonth)) {
                    $monthNum = (int)$rawMonth;
                } else {
                    $monthNum = (int) Carbon::parse('1 '.$rawMonth)->format('n');
                }

                return Carbon::create($year, $monthNum, 1)->format('Y-m-d');
            } catch (\Throwable $e) { /* ignore */ }
        }

        if ($endDate) return $endDate;
        return now()->format('Y-m-d');
    }
}
