<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\MasterFile;

class KltgInlineController extends Controller
{
    public function update(Request $request)
    {
        // Request dari JS inline-edit
        // { id, column, value, ...konteks }
        $data = $request->validate([
            'id'     => ['required','integer','exists:master_files,id'],
            'column' => ['required','string'],
            'value'  => ['nullable'],
            // optional konteks table detail
            'year'   => ['nullable','integer'],
            'month'  => ['nullable','string'], // ex: 'January'/'Jan'/'07' — kita normalize nanti
            'type'   => ['nullable','string'], // kategori/tipe jika diperlukan
        ]);

        $id     = (int)$data['id'];
        $column = $data['column'];
        $value  = $data['value'];

        // --- 2A. Kolom yang memang tinggal nempel di master_files
        $masterCols = [
            'barter','product_category','remarks',
            'location','date','date_finish','invoice_date',
        ];

        // Jika kolomnya milik master_files -> update langsung
        if (in_array($column, $masterCols, true)) {
            // Normalisasi tanggal jika perlu
            $dateCols = ['date','date_finish','invoice_date'];
            if (in_array($column, $dateCols, true)) {
                if ($value) {
                    try {
                        $value = Carbon::parse($value)->format('Y-m-d');
                    } catch (\Throwable $e) {
                        return response()->json(['ok'=>false,'message'=>'Invalid date'], 422);
                    }
                } else {
                    $value = null;
                }
            }

            MasterFile::whereKey($id)->update([$column => $value]);

            return response()->json(['ok'=>true, 'scope'=>'master_files']);
        }

        // --- 2B. Selain itu, anggap sebagai field “detail bulanan” KLTG
        // Skema dari kamu (disediakan sebelumnya):
        // kltg_monthly_details:
        // id, master_file_id, year, month, category, type, field_type,
        // value, value_text, value_date, is_date, status, created_at, updated_at
        $year  = $data['year'] ?? (int) now('Asia/Kuala_Lumpur')->year;
        $month = $this->normalizeMonth($data['month'] ?? null); // '01'..'12'
        $type  = $data['type'] ?? null;

        if (!$month) {
            // Kalau view kamu tidak kirim month karena ini bukan field bulanan,
            // balikin 422 supaya ketahuan di Network tab.
            return response()->json(['ok'=>false,'message'=>'Missing month for detail field'], 422);
        }

        // Apakah kolomnya date-ish untuk table detail?
        $detailDateCols = ['some_detail_date']; // isi kalau memang ada date detail
        $isDate = in_array($column, $detailDateCols, true);

        $payload = [
            'master_file_id' => $id,
            'year'           => (int)$year,
            'month'          => $month,            // simpan sebagai '01'..'12'
            'field_type'     => $column,           // kolom yg diedit jadi “field_type”
            'type'           => $type,
            'is_date'        => $isDate ? 1 : 0,
            'updated_at'     => now(),
        ];

        if ($isDate) {
            if ($value) {
                try {
                    $payload['value_date'] = Carbon::parse($value)->format('Y-m-d');
                } catch (\Throwable $e) {
                    return response()->json(['ok'=>false,'message'=>'Invalid date'], 422);
                }
            } else {
                $payload['value_date'] = null;
            }
            $payload['value'] = null;
            $payload['value_text'] = null;
        } else {
            // Non-date: simpan di value/value_text sesuai panjang
            $payload['value'] = is_string($value) && strlen($value) > 255 ? null : $value;
            $payload['value_text'] = is_string($value) && strlen($value) > 255 ? $value : null;
            $payload['value_date'] = null;
        }

        // Upsert berdasar kombinasi unik: (master_file_id, year, month, field_type)
        // Pastikan ada index unik ini di DB untuk aman dari duplikat.
        DB::table('kltg_monthly_details')->updateOrInsert(
            [
                'master_file_id' => $id,
                'year'           => (int)$year,
                'month'          => $month,
                'field_type'     => $column,
            ],
            $payload + ['created_at' => now()]
        );

        return response()->json(['ok'=>true, 'scope'=>'kltg_monthly_details']);
    }

    private function normalizeMonth(?string $m): ?string
    {
        if (!$m || $m === '') return null;

        $map = [
            'january'=>'01','jan'=>'01','01'=>'01','1'=>'01',
            'february'=>'02','feb'=>'02','02'=>'02','2'=>'02',
            'march'=>'03','mar'=>'03','03'=>'03','3'=>'03',
            'april'=>'04','apr'=>'04','04'=>'04','4'=>'04',
            'may'=>'05','05'=>'05','5'=>'05',
            'june'=>'06','jun'=>'06','06'=>'06','6'=>'06',
            'july'=>'07','jul'=>'07','07'=>'07','7'=>'07',
            'august'=>'08','aug'=>'08','08'=>'08','8'=>'08',
            'september'=>'09','sep'=>'09','09'=>'09','9'=>'09',
            'october'=>'10','oct'=>'10','10'=>'10',
            'november'=>'11','nov'=>'11','11'=>'11',
            'december'=>'12','dec'=>'12','12'=>'12',
        ];
        $k = strtolower(trim($m));
        return $map[$k] ?? null;
    }
}
