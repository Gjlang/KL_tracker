<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OutdoorCoordinatorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // keep your policies/guards as-is at the controller/route level
    }

    public function rules(): array
    {
        return [
            'master_file_id'   => ['nullable','integer','min:1'],
            'outdoor_item_id'  => ['nullable','integer','min:1'],
            'year'             => ['nullable','integer','min:2000','max:2100'],
            'month'            => ['nullable','integer','between:1,12'],

            // text controls
            'client'                       => ['nullable','string','max:255'],
            'product'                      => ['nullable','string','max:255'],
            'site'                         => ['nullable','string','max:255'],
            'payment'                      => ['nullable','string','max:255'],
            'material'                     => ['nullable','string','max:255'],
            'artwork'                      => ['nullable','string','max:255'],
            'received_approval_note'       => ['nullable','string','max:255'],
            'sent_to_printer_note'         => ['nullable','string','max:255'],
            'collection_printer_note'      => ['nullable','string','max:255'],
            'installation_note'            => ['nullable','string','max:255'],
            'dismantle_note'               => ['nullable','string','max:255'],
            'next_follow_up_note'          => ['nullable','string','max:255'],
            'remarks'                      => ['nullable','string'],

            // date controls
            'site_date'                    => ['nullable','date'],
            'payment_date'                 => ['nullable','date'],
            'material_date'                => ['nullable','date'],
            'artwork_date'                 => ['nullable','date'],
            'received_approval'            => ['nullable','date'],
            'sent_to_printer'              => ['nullable','date'],
            'collection_printer'           => ['nullable','date'],
            'installation'                 => ['nullable','date'],
            'dismantle'                    => ['nullable','date'],
            'next_follow_up'               => ['nullable','date'],

            // enum
            'status' => ['nullable','in:pending,ongoing,completed'],

            // monthly flags stay untouched
            'month_jan' => ['nullable','string','max:255'],
            'month_feb' => ['nullable','string','max:255'],
            // ... keep your existing month_* fields as-is
        ];
    }

    public function validated($key = null, $default = null)
    {
        // Return only keys we explicitly validate to avoid accidental mass-assign
        return parent::validated();
    }
}
