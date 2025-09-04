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
        $rows = self::readRowsWithHeaders($file->getRealPath());

        $columns = Schema::getColumnListing((new MasterFile)->getTable());
        $has     = fn($name) => in_array($name, $columns, true);

        $jobTarget = $has('job') ? 'job' : ($has('job_status') ? 'job_status' : ($has('remarks') ? 'remarks' : null));
        $artTarget = $has('artwork') ? 'artwork' : ($has('artwork_status') ? 'artwork_status' : null);

        // Natural key stays the same
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

            $r = self::slugKeysUnderscore($row);

            // ---- Source fields ----
            $company = self::text($r['company_name'] ?? $r['company'] ?? null);

            // Accept client OR person-in-charge as alias for client (fallback)
            $clientRaw = $r['client'] ?? null;
            $picRaw    = $r['person_in_charge']
                      ?? $r['personincharge']
                      ?? $r['pic']
                      ?? $r['penanggung_jawab']
                      ?? $r['penanggungjawab']
                      ?? null;

            $client  = self::text($clientRaw ?? $picRaw ?? null);
            $product = self::text($r['product'] ?? null);
            $status  = self::text($r['status'] ?? null);
            $traffic = self::text($r['traffic'] ?? null);
            $jobIn   = self::text($r['job'] ?? $r['job_status'] ?? null);
            $artIn   = self::text($r['artwork'] ?? $r['artwork_status'] ?? null);
            $invNo   = self::text($r['invoice_number'] ?? $r['invoice_no'] ?? null);
            $loc     = self::text($r['location'] ?? null);
            $category= self::inferCategory($product, $r['product_category'] ?? null);

            // NEW: email + contact number (common aliases)
            $email = self::text(
                $r['email']
                ?? $r['e_mail']
                ?? $r['email_address']
                ?? $r['mail']
                ?? null
            );

            $contact = self::text(
                $r['contact_number']
                ?? $r['contact_no']
                ?? $r['contact']
                ?? $r['phone']
                ?? $r['phone_number']
                ?? $r['no_hp']
                ?? $r['no_tel']
                ?? $r['tel']
                ?? $r['mobile']
                ?? null
            );

            $createdAt = self::parseDate($r['date_created'] ?? $r['created_at'] ?? null);
            $startDate = self::parseDate($r['start_date']   ?? $r['date'] ?? null);
            $endDate   = self::parseDate($r['end_date']     ?? $r['date_finish'] ?? null);
            $invDate   = self::parseDate($r['invoice_date'] ?? $r['invoice_at'] ?? null);
            $rawMonth  = $r['month'] ?? null;

            // If date is still missing, compute fallback date first
            if ($startDate === null && $has('date')) {
                $startDate = self::getFallbackDate($createdAt, $rawMonth, $endDate);
            }

            // ✅ Ensure month AFTER finalizing all dates
            $month = self::normalizeMonth($rawMonth, $startDate);

            // Final safety: if month is still null but we have ANY date, derive from that.
            if ($has('month') && $month === null) {
                $fallbackAnyDate = $startDate ?? $endDate ?? $invDate ?? $createdAt ?? now()->format('Y-m-d');
                $month = Carbon::parse($fallbackAnyDate)->format('F'); // e.g., "September"
            }

            // Duration (optional)
            $duration  = $r['duration'] ?? null;
            if ($duration === null && $startDate && $endDate) {
                try { $duration = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)); } catch (\Throwable $e) {}
            }

            // ---- Build payload (only existing columns + non-null) ----
            $payload = [];
            if ($has('company') && $company !== null)           $payload['company'] = $company;

            // Store client (with PIC fallback). If the table also has a PIC column, put PIC there too.
            if ($has('client') && $client !== null)             $payload['client'] = $client;
            if ($has('person_in_charge') && $picRaw !== null)   $payload['person_in_charge'] = self::text($picRaw);
            if ($has('pic') && $picRaw !== null)                $payload['pic'] = self::text($picRaw);

            if ($has('product') && $product !== null)           $payload['product'] = $product;
            if ($has('product_category') && $category !== null) $payload['product_category'] = $category;
            if ($has('location') && $loc !== null)              $payload['location'] = $loc;
            if ($has('status') && $status !== null)             $payload['status'] = $status;
            if ($has('traffic') && $traffic !== null)           $payload['traffic'] = $traffic;

            // NEW: email/contact mapped to whatever exists in schema
            if ($has('email') && $email !== null)               $payload['email'] = $email;
            elseif ($has('email_address') && $email !== null)   $payload['email_address'] = $email;

            // Prefer more specific columns first
            if ($has('contact_number') && $contact !== null)    $payload['contact_number'] = $contact;
            elseif ($has('phone') && $contact !== null)         $payload['phone'] = $contact;
            elseif ($has('contact') && $contact !== null)       $payload['contact'] = $contact;
            elseif ($has('tel') && $contact !== null)           $payload['tel'] = $contact;

            if ($has('date'))                                   $payload['date'] = $startDate;
            if ($has('date_finish') && $endDate !== null)       $payload['date_finish'] = $endDate;
            if ($has('invoice_date') && $invDate !== null)      $payload['invoice_date'] = $invDate;
            if ($has('invoice_number') && $invNo !== null)      $payload['invoice_number'] = $invNo;
            if ($has('duration') && is_numeric($duration))      $payload['duration'] = (int)$duration;

            // ✅ Always include month if the column exists (NOT NULL-safe)
            if ($has('month') && $month !== null)               $payload['month'] = $month;

            if ($jobTarget && $jobIn !== null)                  $payload[$jobTarget] = $jobIn;
            if ($artTarget && $artIn !== null)                  $payload[$artTarget] = $artIn;

            if ($has('created_at'))                             $payload['created_at'] = $createdAt ?: now();
            if ($has('updated_at'))                             $payload['updated_at'] = now();

            // ---- Upsert by natural key ----
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

    private static function readRowsWithHeaders(string $path): array
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheet(0);

        $raw = $sheet->toArray(null, true, true, true);
        if (empty($raw)) return [];

        $headersRow = array_shift($raw);
        $headers = [];
        foreach ($headersRow as $col => $title) {
            $title = is_string($title) ? trim($title) : $title;
            $headers[$col] = $title !== null && $title !== '' ? $title : $col;
        }

        $rows = [];
        foreach ($raw as $row) {
            $assoc = [];
            foreach ($row as $col => $val) {
                $header = $headers[$col] ?? $col;
                $assoc[$header] = $val;
            }
            $rows[] = $assoc;
        }

        return $rows;
    }

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

    private static function parseDate($v)
    {
        if ($v === null || $v === '') return null;

        if (is_numeric($v)) {
            try {
                $dt = XlsDate::excelToDateTimeObject((float)$v);
                return Carbon::instance($dt)->format('Y-m-d');
            } catch (\Throwable $e) {}
        }

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
            } catch (\Throwable $e) {}
        }

        if ($endDate) return $endDate;
        return now()->format('Y-m-d');
    }
}
