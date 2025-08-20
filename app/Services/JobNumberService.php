<?php

namespace App\Services;

use App\Models\MasterFile;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JobNumberService
{
    /**
     * Build: PREFIX-MMYY-SEQ4
     * - MMYY comes from "created_at" time (use app timezone).
     * - SEQ4 is global per-month across ALL prefixes.
     */
    public function generate(string $category, ?string $product): string
    {
        $mmyy   = $this->mmyyFromNow();         // <-- from created_at/now()
        $prefix = $this->prefixFor($category, $product);

        // Get max trailing 4-digit suffix for THIS MMYY across all rows
        // Order by numeric RIGHT(...,4) to avoid chunking & misses
        $maxSuffix = MasterFile::query()
            ->where('job_number', 'like', '%-' . $mmyy . '-%')
            ->orderByRaw('CAST(RIGHT(job_number, 4) AS UNSIGNED) DESC')
            ->value('job_number');

        $next = 1;
        if ($maxSuffix && preg_match('/(\d{4})$/', $maxSuffix, $m)) {
            $next = (int)$m[1] + 1;
        }
        if ($next > 9999) {
            abort(422, 'Monthly job number sequence exceeded 9999.');
        }

        return sprintf('%s-%s-%04d', $prefix, $mmyy, $next);
    }

    /** MMYY from "now" in app timezone (mirrors created_at). */
    protected function mmyyFromNow(): string
    {
        $tz = config('app.timezone', 'Asia/Kuala_Lumpur');
        return Carbon::now($tz)->format('my'); // e.g. Aug 2025 → "0825"
    }

    /** Map category/product to prefix. */
    protected function prefixFor(string $category, ?string $product): string
    {
        $cat = strtolower(trim($category));

        if (str_contains($cat, 'kltg')) {
            return 'BP';
        }
        if (str_contains($cat, 'social') || str_contains($cat, 'media')) {
            return 'SM';
        }

        // Outdoor/other → use short product code; fallback OD
        $raw   = strtoupper((string)$product);
        $token = preg_split('/[\s\/\-]+/', $raw)[0] ?? '';
        $code  = preg_replace('/[^A-Z0-9]/', '', $token);
        return $code && strlen($code) <= 6 ? $code : 'OD';
    }
}
