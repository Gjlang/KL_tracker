{{-- coordinators/partials/_tab_table.blade.php --}}
@php
  // $section, $rows, $year, $month di-passing dari parent view

  $columns = match($section) {
    'content' => [
      ['key'=>'no', 'label'=>'No', 'width' => 'w-16'],
      ['key'=>'company', 'label'=>'Company', 'width' => 'w-32'],
      ['key'=>'client', 'label'=>'Client Name', 'width' => 'w-40'],
      ['key'=>'product', 'label'=>'Package (Product)', 'width' => 'w-48'],
      ['key'=>'total_artwork_date', 'label'=>'Total Artwork Date', 'type'=>'date', 'width' => 'w-44'],
      ['key'=>'pending_date', 'label'=>'Pending Date', 'type'=>'date', 'width' => 'w-44'],
      ['key'=>'draft_wa', 'label'=>'Draft WA', 'type'=>'number', 'width' => 'w-28'],
      ['key'=>'approved', 'label'=>'Approved', 'type'=>'number', 'width' => 'w-28'],
      ['key'=>'remarks', 'label'=>'Remarks', 'type'=>'text', 'width' => 'w-64'],
    ],
    'editing' => [
      ['key'=>'no', 'label'=>'No', 'width' => 'w-16'],
      ['key'=>'company', 'label'=>'Company', 'width' => 'w-32'],
      ['key'=>'client', 'label'=>'Client Name', 'width' => 'w-40'],
      ['key'=>'product', 'label'=>'Package (Product)', 'width' => 'w-48'],
      ['key'=>'total_artwork_date', 'label'=>'Total Artwork Date', 'type'=>'date', 'width' => 'w-44'],
      ['key'=>'pending_date', 'label'=>'Pending Date', 'type'=>'date', 'width' => 'w-44'],
      ['key'=>'draft_wa', 'label'=>'Draft WA', 'type'=>'number', 'width' => 'w-28'],
      ['key'=>'approved', 'label'=>'Approved', 'type'=>'number', 'width' => 'w-28'],
      ['key'=>'remarks', 'label'=>'Remarks', 'type'=>'text', 'width' => 'w-64'],
    ],
    'schedule' => [
      ['key'=>'no', 'label'=>'No', 'width' => 'w-16'],
      ['key'=>'company', 'label'=>'Company', 'width' => 'w-32'],
      ['key'=>'client', 'label'=>'Client Name', 'width' => 'w-40'],
      ['key'=>'product', 'label'=>'Package (Product)', 'width' => 'w-48'],
      ['key'=>'total_artwork_date', 'label'=>'Total Artwork Date', 'type'=>'date', 'width' => 'w-44'],
      ['key'=>'crm_date', 'label'=>'CRM Date', 'type'=>'date', 'width' => 'w-44'],
      ['key'=>'meta_ads_manager_date', 'label'=>'Meta/Ads Manager Date', 'type'=>'date', 'width' => 'w-44'],
      ['key'=>'tiktok_ig_draft', 'label'=>'TikTok/IG Draft', 'type'=>'number', 'width' => 'w-32'],
      ['key'=>'remarks', 'label'=>'Remarks', 'type'=>'text', 'width' => 'w-64'],
    ],
    'report' => [
      ['key'=>'no', 'label'=>'No', 'width' => 'w-16'],
      ['key'=>'company', 'label'=>'Company', 'width' => 'w-32'],
      ['key'=>'client', 'label'=>'Client Name', 'width' => 'w-40'],
      ['key'=>'product', 'label'=>'Package (Product)', 'width' => 'w-48'],
      ['key'=>'pending_date', 'label'=>'Pending Date', 'type'=>'date', 'width' => 'w-44'],
      ['key'=>'completed_date', 'label'=>'Completed Date', 'type'=>'date', 'width' => 'w-44'],
      ['key'=>'remarks', 'label'=>'Remarks', 'type'=>'text', 'width' => 'w-64'],
    ],
    'valueadd' => [
      ['key'=>'no', 'label'=>'No', 'width' => 'w-16'],
      ['key'=>'company', 'label'=>'Company', 'width' => 'w-32'],
      ['key'=>'client', 'label'=>'Client Name', 'width' => 'w-40'],
      ['key'=>'quota', 'label'=>'Quota (Textbox)', 'type'=>'text', 'width' => 'w-48'],
      ['key'=>'completed', 'label'=>'Completed', 'type'=>'number', 'width' => 'w-28'],
      ['key'=>'remarks', 'label'=>'Remarks', 'type'=>'text', 'width' => 'w-64'],
    ],
    default => []
  };
@endphp

<div class="w-full">
  @if (is_null($month))
    <div class="flex items-center p-4 mb-6 text-amber-800 bg-amber-50 border border-amber-200 rounded-lg">
      <svg class="w-5 h-5 mr-3 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
      </svg>
      <div>
        <strong>Filter Required:</strong> Please select both <strong>Month</strong> and <strong>Year</strong> to view and edit data.
      </div>
    </div>
  @endif

  <div class="overflow-x-auto shadow ring-1 ring-black ring-opacity-5 rounded-lg">
    <table class="min-w-full divide-y divide-gray-300 bg-white">
      <thead class="bg-gray-50">
        <tr>
          @foreach ($columns as $col)
            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-b border-gray-200">
              <div class="flex items-center space-x-1">
                <span>{{ $col['label'] }}</span>
                @if (isset($col['type']) && $col['type'] === 'date')
                  <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                @elseif (isset($col['type']) && $col['type'] === 'number')
                  <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                  </svg>
                @endif
              </div>
            </th>
          @endforeach
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        @forelse ($rows as $i => $row)
          <tr class="hover:bg-gray-50 transition-colors duration-150">
            @foreach ($columns as $col)
              @php $key = $col['key']; @endphp

              {{-- Static cells --}}
              @if ($key === 'no')
                <td class="px-4 py-3 text-sm font-medium text-gray-900 border-b border-gray-100">
                  <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 rounded-full text-xs font-semibold">
                    {{ $loop->iteration }}
                  </span>
                </td>
              @elseif (in_array($key, ['company','client','product','quota']))
                <td class="px-4 py-3 text-sm text-gray-900 border-b border-gray-100">
                  <div class="max-w-xs truncate" title="{{ $row->{$key} }}">
                    {{ $row->{$key} ?: '—' }}
                  </div>
                </td>

              {{-- Editable cells --}}
              @else
                @php
                  $type = $col['type'] ?? 'text';
                  $val  = $row->{$key} ?? null;
                  $width = $col['width'] ?? 'w-full';
                @endphp
                <td class="px-4 py-3 text-sm text-gray-900 border-b border-gray-100">
                  <div
                    x-data="{
                        state: 'idle',
                        val: @js($val),
                        originalVal: @js($val),
                        async save(e) {
                          if ({{ $month ? 'false' : 'true' }}) return;
                          if (e.target.value === this.originalVal) {
                            this.state = 'idle';
                            return;
                          }

                          this.state = 'saving';
                          const ok = await window.mediaUpsert({
                            section: @js($section),
                            master_file_id: {{ (int)$row->master_file_id }},
                            year: {{ (int)$year }},
                            month: {{ (int)($month ?? 0) }},
                            field: @js($key),
                            value: e.target.value
                          });

                          this.state = ok ? 'saved' : 'error';
                          if (ok) {
                            this.val = e.target.value;
                            this.originalVal = e.target.value;
                            setTimeout(() => { this.state = 'idle'; }, 2000);
                          }
                        }
                    }"
                    class="flex items-center space-x-2"
                  >
                    @if ($type === 'date')
                      <input type="date"
                             class="flex-1 border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 disabled:bg-gray-100 disabled:cursor-not-allowed transition-colors {{ $width }}"
                             :value="val"
                             @change="save($event)"
                             {{ $month ? '' : 'disabled' }}>
                    @elseif ($type === 'number')
                      <input type="number" min="0"
                             class="flex-1 border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 disabled:bg-gray-100 disabled:cursor-not-allowed transition-colors {{ $width }}"
                             :value="val"
                             @change="save($event)"
                             {{ $month ? '' : 'disabled' }}>
                    @else
                      <input type="text"
                             class="flex-1 border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 disabled:bg-gray-100 disabled:cursor-not-allowed transition-colors {{ $width }}"
                             :value="val"
                             @change="save($event)"
                             {{ $month ? '' : 'disabled' }}>
                    @endif

                    {{-- Status Indicator --}}
                    <div class="flex-shrink-0 w-16">
                      <span class="inline-flex items-center text-xs font-medium"
                            :class="{
                              'text-gray-400': state === 'idle',
                              'text-blue-600': state === 'saving',
                              'text-green-600': state === 'saved',
                              'text-red-600': state === 'error'
                            }">
                        <svg x-show="state === 'saving'" class="w-3 h-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24">
                          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <svg x-show="state === 'saved'" class="w-3 h-3 mr-1 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <svg x-show="state === 'error'" class="w-3 h-3 mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        <span x-text="state === 'idle' ? '' : (state === 'saving' ? 'Saving...' : (state === 'saved' ? 'Saved!' : 'Error'))">
                        </span>
                      </span>
                    </div>
                  </div>
                </td>
              @endif
            @endforeach
          </tr>
        @empty
          <tr>
            <td colspan="{{ count($columns) }}" class="px-4 py-12 text-center">
              <div class="flex flex-col items-center justify-center text-gray-500">
                <svg class="w-12 h-12 mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 009.586 13H7"></path>
                </svg>
                <h3 class="text-sm font-medium text-gray-900 mb-1">No data available</h3>
                <p class="text-sm text-gray-500">
                  {{ is_null($month) ? 'Please select Month & Year from the filters above.' : 'No records found for the selected filters.' }}
                </p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Table Footer --}}
  @if($rows->isNotEmpty())
    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 border-t border-gray-200 rounded-b-lg">
      <div class="text-sm text-gray-700">
        Showing <span class="font-medium">{{ $rows->count() }}</span>
        {{ Str::plural('record', $rows->count()) }} for
        <span class="font-medium">{{ ucfirst($section) }}</span>
      </div>
      <div class="text-sm text-gray-500">
        {{ $month ? date('F Y', mktime(0, 0, 0, $month, 1, $year)) : 'No period selected' }}
      </div>
    </div>
  @endif
</div>
