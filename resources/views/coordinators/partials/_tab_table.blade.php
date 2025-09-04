{{-- resources/views/coordinators/partials/_tab_table.blade.php --}}

@php
$colsByTab = [
  'content'  => ['No','Company','Client Name','Package (Product)','Total Artwork (Date)','Pending (Date)','Draft WA','Approved','Remarks'],
  'editing'  => ['No','Company','Client Name','Package (Product)','Total Artwork (Date)','Pending (Date)','Draft WA','Approved','Remarks'],
  'schedule' => ['No','Company','Client Name','Package (Product)','Total Artwork (Date)','CRM (Date)','Meta/Ads Manager (Date)','TikTok/IG Draft','Remarks'],
  'report'   => ['No','Company','Client Name','Package (Product)','Pending (Date)','Completed (Date)','Remarks'],
  'valueadd' => ['No','Company','Client Name','Quota','Completed','Remarks'],
];
$headers = $colsByTab[$activeTab] ?? [];
@endphp

<div class="overflow-hidden">
  @if(count($headers) > 0)
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            @foreach($headers as $header)
              <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                {{ $header }}
              </th>
            @endforeach
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          @forelse($rows as $index => $row)
            @if($activeTab === 'content' || $activeTab === 'editing')
              <tr class="hover:bg-gray-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->company ?? '' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->client ?? '' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->product ?? '' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->total_artwork_date ?? '' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->pending_date ?? '' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->draft_wa ?? '' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->approved ?? '' }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $row->remarks ?? '' }}</td>
              </tr>
            @elseif($activeTab === 'schedule')
              <tr class="hover:bg-gray-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->company ?? '' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->client ?? '' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->product ?? '' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->total_artwork_date ?? '' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->crm_date ?? '' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->meta_ads_manager_date ?? '' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->tiktok_ig_draft ?? '' }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $row->remarks ?? '' }}</td>
              </tr>
            @elseif($activeTab === 'report')
              <tr class="hover:bg-gray-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->company ?? '' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->client ?? '' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->product ?? '' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->pending_date ?? '' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->completed_date ?? '' }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $row->remarks ?? '' }}</td>
              </tr>
            @elseif($activeTab === 'valueadd')
              <tr class="hover:bg-gray-50 transition-colors duration-150">
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $index + 1 }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->company ?? '' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->client ?? '' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->quota ?? '' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $row->completed ?? '' }}</td>
                <td class="px-6 py-4 text-sm text-gray-900">{{ $row->remarks ?? '' }}</td>
              </tr>
            @endif
          @empty
            <tr>
              <td colspan="{{ count($headers) }}" class="px-6 py-12 text-center">
                <div class="text-gray-400">
                  <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <h3 class="mt-2 text-sm font-medium text-gray-900">No data available</h3>
                  <p class="mt-1 text-sm text-gray-500">
                    No data for the selected filters. Try selecting a different month/year or add new data.
                  </p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  @else
    <div class="text-center py-12">
      <div class="text-gray-400">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">Invalid tab</h3>
        <p class="mt-1 text-sm text-gray-500">The selected tab is not configured properly.</p>
      </div>
    </div>
  @endif
</div>
