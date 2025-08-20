<?php

namespace App\Imports;

use App\Models\MasterFile;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

HeadingRowFormatter::default('slug'); // "Date Created" -> "date_created"

class MasterFileImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public function collection(Collection $rows)
    {
        // Actual columns in the table (prevents "Unknown column" errors)
        $columns = Schema::getColumnListing((new MasterFile)->getTable());
        $has     = fn($name) => in_array($name, $columns, true);

        // Decide where to store "Job" and "Artwork"
        $jobTarget = $has('job') ? 'job' : ($has('job_status') ? 'job_status' : ($has('remarks') ? 'remarks' : null));
        $artTarget = $has('artwork') ? 'artwork' : ($has('artwork_status') ? 'artwork_status' : null);

        // Natural key for updateOrCreate (use what you actually rely on)
        $naturalKey = array_values(array_filter([
            $has('company') ? 'company' : null,
            $has('client') ? 'client' : null,
            $has('product') ? 'product' : null,
            $has('month') ? 'month' : null,
            $has('date') ? 'date' : null,      // Start Date
        ]));

        foreach ($rows as $r) {
            // Read source fields (slugged names)
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
            $startDate = self::parseDate($r['start_date']   ?? $r['date'] ?? null);        // Start -> date
            $endDate   = self::parseDate($r['end_date']     ?? $r['date_finish'] ?? null); // End   -> date_finish
            $invDate   = self::parseDate($r['invoice_date'] ?? $r['invoice_at'] ?? null);
            $rawMonth  = $r['month'] ?? null;
            $month     = self::normalizeMonth($rawMonth, $startDate);

            // 🔧 CRITICAL FIX: Only for required 'date' column - ensure it's never null
            if ($startDate === null && $has('date')) {
                $startDate = self::getFallbackDate($createdAt, $rawMonth, $endDate);
            }

            // Duration calculation (but allow null if no data)
            $duration  = $r['duration'] ?? null;
            if ($duration === null && $startDate && $endDate) {
                $duration = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
            }

            // Build row payload (only include fields that have actual values)
            $row = [];
            if ($has('company') && $company !== null)          $row['company']          = $company;
            if ($has('client') && $client !== null)            $row['client']           = $client;
            if ($has('product') && $product !== null)          $row['product']          = $product;
            if ($has('product_category') && $category !== null) $row['product_category'] = $category;
            if ($has('month') && $month !== null)              $row['month']            = $month;
            if ($has('location') && $loc !== null)             $row['location']         = $loc;
            if ($has('status') && $status !== null)            $row['status']           = $status;
            if ($has('traffic') && $traffic !== null)          $row['traffic']          = $traffic;
            if ($has('date'))                                   $row['date']             = $startDate; // Always include (has fallback)
            if ($has('date_finish') && $endDate !== null)      $row['date_finish']      = $endDate;
            if ($has('invoice_date') && $invDate !== null)     $row['invoice_date']     = $invDate;
            if ($has('invoice_number') && $invNo !== null)     $row['invoice_number']   = $invNo;
            if ($has('duration') && $duration !== null && is_numeric($duration)) $row['duration'] = (int)$duration;

            if ($jobTarget && $jobIn !== null)                 $row[$jobTarget]         = $jobIn;
            if ($artTarget && $artIn !== null)                 $row[$artTarget]         = $artIn;

            if ($has('created_at'))                             $row['created_at']       = $createdAt ?: now();
            if ($has('updated_at'))                             $row['updated_at']       = now();

            // Split key & attributes for updateOrCreate
            $key  = array_intersect_key($row, array_flip($naturalKey));
            $attr = array_diff_key($row, array_flip($naturalKey));

            // If we don't have enough key fields, just insert
            if (empty($key)) {
                MasterFile::create($row);
            } else {
                MasterFile::updateOrCreate($key, $attr);
            }
        }
    }

    // 🔧 NEW: Get fallback date when startDate is null
    private static function getFallbackDate($createdAt, $rawMonth, $endDate)
    {
        // Fallback 1: Use createdAt if available
        if ($createdAt) {
            return $createdAt;
        }

        // Fallback 2: Use first day of the month if month is provided
        if ($rawMonth) {
            try {
                $year = now()->year; // Default to current year

                // If we have end date, use its year
                if ($endDate) {
                    $year = Carbon::parse($endDate)->year;
                }

                // Convert month to number
                if (is_numeric($rawMonth)) {
                    $monthNum = (int)$rawMonth;
                } else {
                    $monthNum = Carbon::parse('1 ' . $rawMonth)->format('n');
                }

                return Carbon::create($year, $monthNum, 1)->format('Y-m-d');
            } catch (\Throwable $e) {
                // Continue to next fallback
            }
        }

        // Fallback 3: Use endDate if available
        if ($endDate) {
            return $endDate;
        }

        // Fallback 4: Use today's date (last resort)
        return now()->format('Y-m-d');
    }

    private static function text($v){ return $v === null ? null : trim((string)$v); }

    private static function parseDate($v){
        if ($v === null || $v === '') return null;
        if (is_numeric($v)) { // Excel serial date
            try { $dt = ExcelDate::excelToDateTimeObject($v); return Carbon::instance($dt)->format('Y-m-d'); }
            catch (\Throwable $e) {}
        }
        try { return Carbon::parse($v)->format('Y-m-d'); } catch (\Throwable $e) { return null; }
    }

    private static function normalizeMonth($m, $fallback = null){
        if ($m === null || $m === '') return $fallback ? Carbon::parse($fallback)->format('F') : null;
        if (is_numeric($m)) return Carbon::create(null, (int)$m, 1)->format('F');
        try { return Carbon::parse('1 '.$m)->format('F'); } catch (\Throwable $e) { return (string)$m; }
    }

    private static function inferCategory($product, $explicit){
        $exp = strtolower((string)$explicit);
        if ($exp) return ucfirst($exp);
        $p = strtolower((string)$product);
        if (str_contains($p, 'kltg')) return 'KLTG';
        if (str_contains($p, 'fb') || str_contains($p, 'ig') || str_contains($p, 'media')) return 'Media';
        return 'Outdoor';
    }
}
