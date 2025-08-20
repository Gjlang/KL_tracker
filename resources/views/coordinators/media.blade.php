{{-- <x-app-layout>
  <div class="flex min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-100/50">
    @include('partials.sidebar')
    <main class="flex-1 overflow-y-auto">
      <div class="p-4 md:p-8 max-w-full">
        <!-- Enhanced Header Section -->
        <div class="mb-8 relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-700 p-8 text-white shadow-2xl">
          <div class="absolute inset-0 bg-gradient-to-r from-black/20 to-transparent"></div>
          <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="flex items-center justify-center w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl shadow-lg">
                <span class="text-3xl">📹</span>
              </div>
              <div>
                <h1 class="text-4xl font-bold mb-2 tracking-tight">
                  Media Monthly Ongoing Job
                </h1>
                <p class="text-blue-100 text-lg font-medium">Manage and track your media projects efficiently</p>
              </div>
            </div>
            <div class="hidden md:flex items-center space-x-4 text-sm text-blue-100">
              <div class="flex items-center space-x-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-2">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse shadow-sm"></div>
                <span class="font-medium">Live Updates</span>
              </div>
            </div>
          </div>
          <!-- Decorative elements -->
          <div class="absolute -top-4 -right-4 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
          <div class="absolute -bottom-8 -left-8 w-40 h-40 bg-purple-500/10 rounded-full blur-3xl"></div>
        </div>

        {{-- Enhanced Flash Messages --}}
        @if(session('status'))
          <div class="mb-8 p-5 rounded-2xl bg-gradient-to-r from-emerald-50 to-green-50 border-l-4 border-emerald-400 shadow-lg">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <p class="text-emerald-800 font-semibold text-lg">{{ session('status') }}</p>
              </div>
            </div>
          </div>
        @endif

        {{-- Enhanced Media Section --}}
        <section id="media" class="scroll-mt-24">
          @if(isset($monthlyByCategory['Media']) && count($monthlyByCategory['Media']))
            <!-- Modern Section Header -->
            <div class="mb-8 bg-white rounded-2xl p-6 shadow-sm border border-slate-200/60">
              <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="flex items-center space-x-4">
                  <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg">
                    <span class="text-white text-2xl">📹</span>
                  </div>
                  <div>
                    <h4 class="text-2xl font-bold text-slate-800 mb-1">MEDIA Ongoing Job</h4>
                    <p class="text-slate-600 font-medium">KL The Guide</p>
                  </div>
                </div>

                <a href="/coordinator/media" class="group relative inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl hover:from-blue-700 hover:to-indigo-700 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                  <div class="flex items-center space-x-3">
                    <span class="text-xl">📱</span>
                    <span class="font-semibold text-lg">Media Coordinator List</span>
                  </div>
                  <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 rounded-2xl transition-opacity duration-300"></div>
                </a>
              </div>
            </div>

            <!-- Enhanced Filter Section -->
            <div class="mb-8 bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
              <div class="bg-gradient-to-r from-slate-50 to-blue-50/30 px-6 py-4 border-b border-slate-200/60">
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                  </div>
                  <h3 class="text-lg font-semibold text-slate-800">Filter Options</h3>
                </div>
              </div>

              <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">
                  <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700 block">Company</label>
                    <select class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 hover:bg-white transition-colors">
                      <option value="">All Companies</option>
                    </select>
                  </div>

                  <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700 block">Platform</label>
                    <select class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 hover:bg-white transition-colors">
                      <option value="">All Platforms</option>
                    </select>
                  </div>

                  <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700 block">Category</label>
                    <select class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 hover:bg-white transition-colors">
                      <option value="">All Categories</option>
                    </select>
                  </div>

                  <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700 block">Month</label>
                    <select class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 hover:bg-white transition-colors">
                      <option value="">All Months</option>
                      <option value="jan">January</option>
                      <option value="feb">February</option>
                      <option value="mar">March</option>
                      <option value="apr">April</option>
                      <option value="may">May</option>
                      <option value="jun">June</option>
                      <option value="jul">July</option>
                      <option value="aug">August</option>
                      <option value="sep">September</option>
                      <option value="oct">October</option>
                      <option value="nov">November</option>
                      <option value="dec">December</option>
                    </select>
                  </div>

                  <div class="flex items-end space-x-3">
                    <button class="px-6 py-3 text-sm font-semibold bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                      Apply Filters
                    </button>
                    <button class="px-6 py-3 text-sm font-medium border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 hover:border-slate-400 transition-colors">
                      Reset
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Enhanced Data Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
              <div class="bg-gradient-to-r from-slate-50 to-blue-50/30 px-6 py-4 border-b border-slate-200/60">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-xl flex items-center justify-center">
                      <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800">Project Data Table</h3>
                  </div>
                  <div class="flex items-center space-x-3">
                    <button class="inline-flex items-center px-5 py-2.5 text-sm font-semibold bg-gradient-to-r from-emerald-600 to-green-600 text-white rounded-xl hover:from-emerald-700 hover:to-green-700 shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                      Export CSV
                    </button>
                  </div>
                </div>
              </div>

              <div class="overflow-x-auto custom-scrollbar" style="max-width: 100%;">
                <table class="table-auto text-sm text-left divide-y divide-slate-200" style="min-width: 2000px; width: max-content;">
                  <thead class="bg-gradient-to-r from-slate-100 via-blue-50 to-indigo-50 sticky top-0 z-10">
                    <tr>
                      <th class="px-6 py-5 text-xs font-bold text-slate-700 uppercase tracking-wider text-center sticky left-0 bg-gradient-to-r from-slate-100 via-blue-50 to-indigo-50 z-20 border-r border-slate-300/60 shadow-sm" style="min-width: 140px; width: 140px;">
                        <div class="flex items-center justify-center space-x-2">
                          <span>📅</span>
                          <span>Date</span>
                        </div>
                      </th>
                      <th class="px-6 py-5 text-xs font-bold text-slate-700 uppercase tracking-wider text-left sticky left-140 bg-gradient-to-r from-slate-100 via-blue-50 to-indigo-50 z-20 border-r border-slate-300/60 shadow-sm" style="min-width: 240px; width: 240px;">
                        <div class="flex items-center space-x-2">
                          <span>🏢</span>
                          <span>Company</span>
                        </div>
                      </th>
                      <th class="px-6 py-5 text-xs font-bold text-slate-700 uppercase tracking-wider text-left border-r border-slate-200" style="min-width: 160px; width: 160px;">
                        <div class="flex items-center space-x-2">
                          <span>📦</span>
                          <span>Product</span>
                        </div>
                      </th>
                      <th class="px-6 py-5 text-xs font-bold text-slate-700 uppercase tracking-wider text-center border-r border-slate-200" style="min-width: 130px; width: 130px;">
                        <div class="flex items-center justify-center space-x-2">
                          <span>🏷️</span>
                          <span>Category</span>
                        </div>
                      </th>
                      <th class="px-6 py-5 text-xs font-bold text-slate-700 uppercase tracking-wider text-center border-r border-slate-200" style="min-width: 140px; width: 140px;">
                        <div class="flex items-center justify-center space-x-2">
                          <span>🌐</span>
                          <span>Platform</span>
                        </div>
                      </th>
                      <th class="px-6 py-5 text-xs font-bold text-slate-700 uppercase tracking-wider text-center border-r border-slate-200" style="min-width: 110px; width: 110px;">
                        <div class="flex items-center justify-center space-x-2">
                          <span>▶️</span>
                          <span>Start</span>
                        </div>
                      </th>
                      <th class="px-6 py-5 text-xs font-bold text-slate-700 uppercase tracking-wider text-center border-r border-slate-200" style="min-width: 110px; width: 110px;">
                        <div class="flex items-center justify-center space-x-2">
                          <span>⏹️</span>
                          <span>End</span>
                        </div>
                      </th>
                      @foreach(['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'] as $month)
                        <th class="px-4 py-5 text-xs font-bold text-slate-700 uppercase tracking-wider text-center bg-gradient-to-b from-blue-50 to-indigo-100/70 border-r border-blue-200/60" style="min-width: 95px; width: 95px;">
                          <div class="flex flex-col items-center space-y-1">
                            <span class="text-blue-600 font-extrabold">{{ strtoupper($month) }}</span>
                            <div class="w-6 h-0.5 bg-blue-400 rounded-full"></div>
                          </div>
                        </th>
                      @endforeach
                      <th class="px-6 py-5 text-xs font-bold text-slate-700 uppercase tracking-wider text-left" style="min-width: 220px; width: 220px;">
                        <div class="flex items-center space-x-2">
                          <span>💬</span>
                          <span>Remarks</span>
                        </div>
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-slate-100">
                    @foreach($monthlyByCategory['Media'] as $index => $job)
                      <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50/50' }} hover:bg-blue-50/60 transition-all duration-300 hover:shadow-sm">
                        <td class="px-6 py-4 text-center sticky left-0 bg-inherit z-10 border-r border-slate-200/60 shadow-sm" style="min-width: 140px; width: 140px;">
                          <div class="bg-slate-100 rounded-xl px-3 py-2 inline-block">
                            <div class="text-sm font-bold text-slate-800">
                              {{ $job->date?->format('d M Y') ?? 'No Date' }}
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 sticky left-140 bg-inherit z-10 border-r border-slate-200/60 shadow-sm" style="min-width: 240px; width: 240px;">
                          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl px-4 py-2">
                            <div class="text-sm font-bold text-slate-900 truncate" title="{{ $job->company ?? 'N/A' }}">
                              {{ $job->company ?? 'N/A' }}
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 border-r border-slate-200" style="min-width: 160px; width: 160px;">
                          <div class="text-sm text-slate-700 font-medium truncate" title="{{ $job->product ?? 'N/A' }}">
                            {{ $job->product ?? 'N/A' }}
                          </div>
                        </td>
                        <td class="px-6 py-4 text-center border-r border-slate-200" style="min-width: 130px; width: 130px;">
                          <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 shadow-sm">
                            {{ $job->product_category ?? 'Uncategorized' }}
                          </span>
                        </td>
                        <td class="px-6 py-4 text-center border-r border-slate-200" style="min-width: 140px; width: 140px;">
                          <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold shadow-sm
                            {{ ($job->location ?? $job->platform ?? 'N/A') === 'N/A' ? 'bg-slate-100 text-slate-600' : 'bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700' }}">
                            {{ $job->location ?? $job->platform ?? 'N/A' }}
                          </span>
                        </td>
                        <td class="px-6 py-4 text-center border-r border-slate-200" style="min-width: 110px; width: 110px;">
                          <div class="bg-green-50 rounded-xl px-3 py-2 inline-block">
                            <div class="text-sm font-bold text-green-700">
                              {{ $job->date?->format('d M') ?? 'TBD' }}
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 text-center relative group border-r border-slate-200" style="min-width:110px;width:110px;">
                          @php
                              // Fixed: Use $job instead of undefined $row
                              $endRaw = $job->date_finish ?? $job->end_date ?? null;
                              $endDate = $endRaw
                                          ? ($endRaw instanceof \Illuminate\Support\Carbon
                                                ? $endRaw
                                                : \Illuminate\Support\Carbon::parse($endRaw))
                                          : null;

                              $today = now();

                              // Defaults
                              $status      = 'upcoming';
                              $statusColor = 'text-blue-600 bg-blue-50';
                              $tooltipText = 'Upcoming deadline';

                              // Check for completed status - look for status in multiple possible fields
                              $statusText = strtolower((string) ($job->status ?? $job->project_status ?? ''));
                              if (in_array($statusText, ['completed','done','finished','complete'])) {
                                  $status      = 'completed';
                                  $statusColor = 'text-green-700 bg-green-50';
                                  $tooltipText = 'Project completed';
                              } elseif ($endDate) {
                                  // Calculate days until deadline
                                  $daysUntil = $today->diffInDays($endDate, false);
                                  if ($daysUntil < 0) {
                                      $status      = 'overdue';
                                      $statusColor = 'text-red-700 bg-red-50';
                                      $tooltipText = 'Project overdue by ' . abs($daysUntil) . ' day(s)';
                                  } elseif ($daysUntil <= 7) {
                                      $status      = 'urgent';
                                      $statusColor = 'text-amber-700 bg-amber-50';
                                      $tooltipText = 'Deadline in ' . $daysUntil . ' day(s)';
                                  } else {
                                      $tooltipText = 'Deadline in ' . $daysUntil . ' day(s)';
                                  }
                              }
                          @endphp
                          <div class="rounded-xl px-3 py-2 inline-flex items-center space-x-2 {{ $statusColor }}">
                            <div class="text-sm font-bold">
                              {{ $endDate?->format('d M') ?? 'TBD' }}
                            </div>
                            @if($endDate)
                              <div class="flex items-center">
                                @if($status === 'completed')
                                  <span class="text-green-500 text-base">✓</span>
                                @elseif($status === 'urgent')
                                  <span class="text-amber-500 text-base">⚠</span>
                                @elseif($status === 'overdue')
                                  <span class="text-red-500 text-base">!</span>
                                @endif
                              </div>
                            @endif
                          </div>
                          @if($endDate)
                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3 px-3 py-2 text-xs font-medium text-white bg-slate-800 rounded-xl opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none whitespace-nowrap z-30 shadow-lg">
                              {{ $tooltipText }}
                              <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-slate-800"></div>
                            </div>
                          @endif
                        </td>
                        @foreach(['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'] as $m)
                          <td class="px-4 py-4 text-center bg-gradient-to-b from-blue-50/30 to-indigo-50/30 hover:from-blue-100/50 hover:to-indigo-100/50 transition-all duration-300 border-r border-blue-200/40" style="min-width: 95px; width: 95px;">
                            @php
                                // Get the month data - check multiple possible field names
                                $monthValue = $job["check_$m"] ??
                                             $job["month_$m"] ??
                                             $job["{$m}_status"] ??
                                             ($job->month === $m ? ($job->status ?? 'Active') : '');

                                // If still empty, check if this month falls within the project timeline
                                if (empty($monthValue) && $job->date && $endDate) {
                                    $monthNum = array_search($m, ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec']) + 1;
                                    $currentYear = $job->date->year;
                                    $monthStart = \Carbon\Carbon::create($currentYear, $monthNum, 1);
                                    $monthEnd = $monthStart->copy()->endOfMonth();

                                    // Check if project runs during this month
                                    if ($job->date <= $monthEnd && $endDate >= $monthStart) {
                                        $monthValue = 'Active';
                                    }
                                }
                            @endphp
                            <input
                                type="text"
                                value="{{ $monthValue }}"
                                data-id="{{ $job->id }}"
                                data-type="master"
                                data-field="check_{{ $m }}"
                                onblur="autoSave(this)"
                                class="w-full bg-white/80 border border-blue-200 text-center text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white focus:shadow-lg px-2 py-2 rounded-xl transition-all duration-200 placeholder-slate-400 hover:bg-white hover:shadow-md"
                                placeholder="•"
                                style="min-width: 75px;"
                            />
                          </td>
                        @endforeach
                        <td class="px-6 py-4" style="min-width: 220px; width: 220px;">
                          <input
                              type="text"
                              value="{{ $job->remarks ?? '' }}"
                              data-id="{{ $job->id }}"
                              data-type="master"
                              data-field="remarks"
                              onblur="autoSave(this)"
                              class="w-full bg-slate-50 border border-slate-200 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white focus:shadow-lg px-4 py-3 rounded-xl transition-all duration-200 placeholder-slate-400 hover:bg-white hover:shadow-md"
                              placeholder="Add remarks..."
                              style="min-width: 200px;"
                          />
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Enhanced Custom Styles -->
            <style>
              /* Sticky column positioning */
              .sticky.left-140 {
                left: 140px;
              }

              /* Custom scrollbar for the table */
              .custom-scrollbar::-webkit-scrollbar {
                height: 12px;
              }

              .custom-scrollbar::-webkit-scrollbar-track {
                background: linear-gradient(to right, #f8fafc, #f1f5f9);
                border-radius: 8px;
                box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
              }

              .custom-scrollbar::-webkit-scrollbar-thumb {
                background: linear-gradient(to right, #e2e8f0, #cbd5e1);
                border-radius: 8px;
                border: 2px solid #f8fafc;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
              }

              .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(to right, #cbd5e1, #94a3b8);
              }

              /* Enhanced transitions and animations */
              * {
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
              }

              /* Input field enhancements */
              input:focus {
                transform: translateY(-1px);
                box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
              }

              input:hover {
                transform: translateY(-0.5px);
              }

              /* Row hover effects */
              tbody tr:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
              }

              /* Button hover effects */
              button:hover {
                transform: translateY(-2px);
              }

              /* Smooth fade-in animation */
              @keyframes slideInUp {
                from {
                  opacity: 0;
                  transform: translateY(30px);
                }
                to {
                  opacity: 1;
                  transform: translateY(0);
                }
              }

              section {
                animation: slideInUp 0.6s ease-out;
              }

              /* Enhanced gradient backgrounds */
              .bg-gradient-to-br {
                background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
              }

              .bg-gradient-to-r {
                background-image: linear-gradient(to right, var(--tw-gradient-stops));
              }

              .bg-gradient-to-b {
                background-image: linear-gradient(to bottom, var(--tw-gradient-stops));
              }

              /* Premium shadow effects */
              .shadow-sm {
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
              }

              .shadow-lg {
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
              }

              .shadow-xl {
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
              }

              .shadow-2xl {
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
              }

              /* Enhanced hover effects with smooth transitions */
              .hover\:shadow-md:hover {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
              }

              .hover\:shadow-xl:hover {
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
              }

              /* Backdrop blur effects for modern glass morphism */
              .backdrop-blur-sm {
                backdrop-filter: blur(4px);
              }

              .backdrop-blur-md {
                backdrop-filter: blur(8px);
              }

              /* Custom month input styling */
              input[data-field*="check_"] {
                background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(248,250,252,0.9));
                border: 1.5px solid rgba(59, 130, 246, 0.2);
              }

              input[data-field*="check_"]:focus {
                background: linear-gradient(135deg, #ffffff, #f8fafc);
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 8px 25px rgba(59, 130, 246, 0.15);
              }

              input[data-field*="check_"]:hover {
                background: linear-gradient(135deg, #ffffff, #f1f5f9);
                border-color: rgba(59, 130, 246, 0.4);
              }

              /* Remarks input styling */
              input[data-field="remarks"] {
                background: linear-gradient(135deg, rgba(248,250,252,0.8), rgba(241,245,249,0.8));
              }

              input[data-field="remarks"]:focus {
                background: linear-gradient(135deg, #ffffff, #f8fafc);
              }

              /* Select dropdown enhancements */
              select {
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
                background-position: right 12px center;
                background-repeat: no-repeat;
                background-size: 16px;
                padding-right: 40px;
              }

              select:focus {
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%233b82f6' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
              }

              /* Responsive design enhancements */
              @media (max-width: 768px) {
                .sticky.left-140 {
                  left: 120px;
                }

                th, td {
                  min-width: 120px !important;
                  width: 120px !important;
                }

                .sticky[style*="130px"] {
                  min-width: 120px !important;
                  width: 120px !important;
                }

                .sticky[style*="240px"] {
                  min-width: 200px !important;
                  width: 200px !important;
                }
              }

              @media (max-width: 640px) {
                .overflow-x-auto {
                  border-radius: 16px;
                }

                th, td {
                  padding-left: 12px;
                  padding-right: 12px;
                }
              }

              /* Loading and interaction states */
              .loading {
                opacity: 0.7;
                pointer-events: none;
              }

              /* Enhanced focus rings */
              .focus-ring:focus {
                outline: none;
                ring: 2px;
                ring-color: rgb(59 130 246);
                ring-offset: 2px;
              }

              /* Tooltip improvements */
              .tooltip {
                position: relative;
              }

              .tooltip::before {
                content: attr(data-tooltip);
                position: absolute;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(15, 23, 42, 0.9);
                color: white;
                padding: 8px 12px;
                border-radius: 8px;
                font-size: 12px;
                font-weight: 500;
                white-space: nowrap;
                opacity: 0;
                pointer-events: none;
                transition: all 0.2s ease;
                z-index: 1000;
              }

              .tooltip:hover::before {
                opacity: 1;
                transform: translateX(-50%) translateY(-4px);
              }

              /* Status indicator animations */
              .animate-pulse-slow {
                animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
              }

              @keyframes pulse {
                0%, 100% {
                  opacity: 1;
                }
                50% {
                  opacity: 0.7;
                }
              }

              /* Modern card styling */
              .card-modern {
                background: linear-gradient(145deg, #ffffff, #f8fafc);
                border: 1px solid rgba(226, 232, 240, 0.8);
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.1);
              }

              .card-modern:hover {
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 2px 4px rgba(0, 0, 0, 0.06);
              }

              /* Auto-save feedback */
              .saving {
                border-color: #f59e0b !important;
                background: linear-gradient(135deg, #fef3c7, #fde68a) !important;
              }

              .saved {
                border-color: #10b981 !important;
                background: linear-gradient(135deg, #d1fae5, #a7f3d0) !important;
              }

              .error {
                border-color: #ef4444 !important;
                background: linear-gradient(135deg, #fecaca, #fca5a5) !important;
              }
            </style>

            <!-- Add JavaScript for auto-save functionality -->
            <script>
              // Auto-save function with visual feedback
              async function autoSave(input) {
                const originalClass = input.className;

                try {
                  // Add saving state
                  input.classList.add('saving');

                  const response = await fetch('/api/update-job', {
                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                      id: input.dataset.id,
                      type: input.dataset.type,
                      field: input.dataset.field,
                      value: input.value
                    })
                  });

                  if (response.ok) {
                    // Success feedback
                    input.className = originalClass;
                    input.classList.add('saved');
                    setTimeout(() => {
                      input.className = originalClass;
                    }, 1500);
                  } else {
                    throw new Error('Save failed');
                  }
                } catch (error) {
                  // Error feedback
                  input.className = originalClass;
                  input.classList.add('error');
                  setTimeout(() => {
                    input.className = originalClass;
                  }, 2000);
                  console.error('Auto-save error:', error);
                }
              }

              // Enhanced UX: Save on Enter key
              document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.matches('input[data-field]')) {
                  e.target.blur(); // Trigger the onblur auto-save
                }
              });

              // Auto-populate month fields based on project timeline
              document.addEventListener('DOMContentLoaded', function() {
                const rows = document.querySelectorAll('tbody tr');

                rows.forEach(row => {
                  const startDateCell = row.querySelector('td:nth-child(6) .text-green-700');
                  const endDateCell = row.querySelector('td:nth-child(7) .text-sm');

                  if (startDateCell && endDateCell) {
                    const monthInputs = row.querySelectorAll('input[data-field*="check_"]');

                    monthInputs.forEach(input => {
                      if (!input.value.trim()) {
                        // Auto-populate based on project timeline
                        const month = input.dataset.field.replace('check_', '');
                        const monthIndex = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'].indexOf(month);

                        // Simple logic: if project spans multiple months, mark as "Active"
                        if (monthIndex >= 0) {
                          input.placeholder = "Active";
                          input.style.opacity = "0.7";
                        }
                      }
                    });
                  }
                });
              });
            </script>
          @endif
        </section>
      </div>
    </main>
  </div>
</x-app-layout> --}}


<x-app-layout>
  <div class="flex min-h-screen bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-100/50">
    @include('partials.sidebar')
    <main class="flex-1 overflow-y-auto">
      <div class="p-4 md:p-8 max-w-full">
        <!-- Enhanced Header Section -->
        <div class="mb-8 relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-700 p-8 text-white shadow-2xl">
          <div class="absolute inset-0 bg-gradient-to-r from-black/20 to-transparent"></div>
          <div class="relative z-10 flex items-center justify-between">
            <div class="flex items-center space-x-4">
              <div class="flex items-center justify-center w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl shadow-lg">
                <span class="text-3xl">📹</span>
              </div>
              <div>
                <h1 class="text-4xl font-bold mb-2 tracking-tight">
                  Media Monthly Ongoing Job
                </h1>
                <p class="text-blue-100 text-lg font-medium">Manage and track your media projects efficiently</p>
              </div>
            </div>
            <div class="hidden md:flex items-center space-x-4 text-sm text-blue-100">
              <div class="flex items-center space-x-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-2">
                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse shadow-sm"></div>
                <span class="font-medium">Live Updates</span>
              </div>
            </div>
          </div>
          <!-- Decorative elements -->
          <div class="absolute -top-4 -right-4 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
          <div class="absolute -bottom-8 -left-8 w-40 h-40 bg-purple-500/10 rounded-full blur-3xl"></div>
        </div>

        {{-- Enhanced Flash Messages --}}
        @if(session('status'))
          <div class="mb-8 p-5 rounded-2xl bg-gradient-to-r from-emerald-50 to-green-50 border-l-4 border-emerald-400 shadow-lg">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
                  <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                  </svg>
                </div>
              </div>
              <div class="ml-4">
                <p class="text-emerald-800 font-semibold text-lg">{{ session('status') }}</p>
              </div>
            </div>
          </div>
        @endif

        {{-- Enhanced Media Section --}}
        <section id="media" class="scroll-mt-24">
          @if(isset($monthlyByCategory['Media']) && count($monthlyByCategory['Media']))
            <!-- Modern Section Header -->
            <div class="mb-8 bg-white rounded-2xl p-6 shadow-sm border border-slate-200/60">
              <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="flex items-center space-x-4">
                  <div class="flex items-center justify-center w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl shadow-lg">
                    <span class="text-white text-2xl">📹</span>
                  </div>
                  <div>
                    <h4 class="text-2xl font-bold text-slate-800 mb-1">MEDIA Ongoing Job</h4>
                    <p class="text-slate-600 font-medium">KL The Guide</p>
                  </div>
                </div>

                <a href="/coordinator/media" class="group relative inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-2xl hover:from-blue-700 hover:to-indigo-700 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                  <div class="flex items-center space-x-3">
                    <span class="text-xl">📱</span>
                    <span class="font-semibold text-lg">Media Coordinator List</span>
                  </div>
                  <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 rounded-2xl transition-opacity duration-300"></div>
                </a>
              </div>
            </div>

            <!-- Enhanced Filter Section -->
            <div class="mb-8 bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
              <div class="bg-gradient-to-r from-slate-50 to-blue-50/30 px-6 py-4 border-b border-slate-200/60">
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                  </div>
                  <h3 class="text-lg font-semibold text-slate-800">Filter Options</h3>
                </div>
              </div>

              <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 mb-6">
                  <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700 block">Company</label>
                    <select class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 hover:bg-white transition-colors">
                      <option value="">All Companies</option>
                    </select>
                  </div>

                  <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700 block">Platform</label>
                    <select class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 hover:bg-white transition-colors">
                      <option value="">All Platforms</option>
                    </select>
                  </div>

                  <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700 block">Category</label>
                    <select class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 hover:bg-white transition-colors">
                      <option value="">All Categories</option>
                    </select>
                  </div>

                  <div class="space-y-2">
                    <label class="text-sm font-medium text-slate-700 block">Month</label>
                    <select class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-slate-50 hover:bg-white transition-colors">
                      <option value="">All Months</option>
                      <option value="jan">January</option>
                      <option value="feb">February</option>
                      <option value="mar">March</option>
                      <option value="apr">April</option>
                      <option value="may">May</option>
                      <option value="jun">June</option>
                      <option value="jul">July</option>
                      <option value="aug">August</option>
                      <option value="sep">September</option>
                      <option value="oct">October</option>
                      <option value="nov">November</option>
                      <option value="dec">December</option>
                    </select>
                  </div>

                  <div class="flex items-end space-x-3">
                    <button class="px-6 py-3 text-sm font-semibold bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                      Apply Filters
                    </button>
                    <button class="px-6 py-3 text-sm font-medium border border-slate-300 text-slate-700 rounded-xl hover:bg-slate-50 hover:border-slate-400 transition-colors">
                      Reset
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Enhanced Data Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 overflow-hidden">
              <div class="bg-gradient-to-r from-slate-50 to-blue-50/30 px-6 py-4 border-b border-slate-200/60">
                <div class="flex items-center justify-between">
                  <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-indigo-100 rounded-xl flex items-center justify-center">
                      <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-800">Project Data Table</h3>
                  </div>
                  <div class="flex items-center space-x-3">
                    <button class="inline-flex items-center px-5 py-2.5 text-sm font-semibold bg-gradient-to-r from-emerald-600 to-green-600 text-white rounded-xl hover:from-emerald-700 hover:to-green-700 shadow-md hover:shadow-lg transition-all duration-200 transform hover:-translate-y-0.5">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                      </svg>
                      Export CSV
                    </button>
                  </div>
                </div>
              </div>

              <div class="overflow-x-auto custom-scrollbar" style="max-width: 100%;">
                <table class="table-auto text-sm text-left divide-y divide-slate-200" style="min-width: 2000px; width: max-content;">
                  <thead class="bg-gradient-to-r from-slate-100 via-blue-50 to-indigo-50 sticky top-0 z-10">
                    <tr>
                      <th class="px-6 py-5 text-xs font-bold text-slate-700 uppercase tracking-wider text-center sticky left-0 bg-gradient-to-r from-slate-100 via-blue-50 to-indigo-50 z-20 border-r border-slate-300/60 shadow-sm" style="min-width: 140px; width: 140px;">
                        <div class="flex items-center justify-center space-x-2">
                          <span>📅</span>
                          <span>Date</span>
                        </div>
                      </th>
                      <th class="px-6 py-5 text-xs font-bold text-slate-700 uppercase tracking-wider text-left sticky left-140 bg-gradient-to-r from-slate-100 via-blue-50 to-indigo-50 z-20 border-r border-slate-300/60 shadow-sm" style="min-width: 240px; width: 240px;">
                        <div class="flex items-center space-x-2">
                          <span>🏢</span>
                          <span>Company</span>
                        </div>
                      </th>
                      <th class="px-6 py-5 text-xs font-bold text-slate-700 uppercase tracking-wider text-left border-r border-slate-200" style="min-width: 160px; width: 160px;">
                        <div class="flex items-center space-x-2">
                          <span>📦</span>
                          <span>Product</span>
                        </div>
                      </th>
                      <th class="px-6 py-5 text-xs font-bold text-slate-700 uppercase tracking-wider text-center border-r border-slate-200" style="min-width: 130px; width: 130px;">
                        <div class="flex items-center justify-center space-x-2">
                          <span>🏷️</span>
                          <span>Category</span>
                        </div>
                      </th>
                      <th class="px-6 py-5 text-xs font-bold text-slate-700 uppercase tracking-wider text-center border-r border-slate-200" style="min-width: 140px; width: 140px;">
                        <div class="flex items-center justify-center space-x-2">
                          <span>🌐</span>
                          <span>Platform</span>
                        </div>
                      </th>
                      <th class="px-6 py-5 text-xs font-bold text-slate-700 uppercase tracking-wider text-center border-r border-slate-200" style="min-width: 110px; width: 110px;">
                        <div class="flex items-center justify-center space-x-2">
                          <span>▶️</span>
                          <span>Start</span>
                        </div>
                      </th>
                      <th class="px-6 py-5 text-xs font-bold text-slate-700 uppercase tracking-wider text-center border-r border-slate-200" style="min-width: 110px; width: 110px;">
                        <div class="flex items-center justify-center space-x-2">
                          <span>⏹️</span>
                          <span>End</span>
                        </div>
                      </th>
                      @foreach(['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'] as $month)
                        <th class="px-4 py-5 text-xs font-bold text-slate-700 uppercase tracking-wider text-center bg-gradient-to-b from-blue-50 to-indigo-100/70 border-r border-blue-200/60" style="min-width: 95px; width: 95px;">
                          <div class="flex flex-col items-center space-y-1">
                            <span class="text-blue-600 font-extrabold">{{ strtoupper($month) }}</span>
                            <div class="w-6 h-0.5 bg-blue-400 rounded-full"></div>
                          </div>
                        </th>
                      @endforeach
                      <th class="px-6 py-5 text-xs font-bold text-slate-700 uppercase tracking-wider text-left" style="min-width: 220px; width: 220px;">
                        <div class="flex items-center space-x-2">
                          <span>💬</span>
                          <span>Remarks</span>
                        </div>
                      </th>
                    </tr>
                  </thead>
                  <tbody class="bg-white divide-y divide-slate-100">
                    @foreach($monthlyByCategory['Media'] as $index => $job)
                      <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-slate-50/50' }} hover:bg-blue-50/60 transition-all duration-300 hover:shadow-sm">
                        <td class="px-6 py-4 text-center sticky left-0 bg-inherit z-10 border-r border-slate-200/60 shadow-sm" style="min-width: 140px; width: 140px;">
                          <div class="bg-slate-100 rounded-xl px-3 py-2 inline-block">
                            <div class="text-sm font-bold text-slate-800">
                              {{ $job->date?->format('d M Y') ?? 'No Date' }}
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 sticky left-140 bg-inherit z-10 border-r border-slate-200/60 shadow-sm" style="min-width: 240px; width: 240px;">
                          <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl px-4 py-2">
                            <div class="text-sm font-bold text-slate-900 truncate" title="{{ $job->company ?? 'N/A' }}">
                              {{ $job->company ?? 'N/A' }}
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 border-r border-slate-200" style="min-width: 160px; width: 160px;">
                          <div class="text-sm text-slate-700 font-medium truncate" title="{{ $job->product ?? 'N/A' }}">
                            {{ $job->product ?? 'N/A' }}
                          </div>
                        </td>
                        <td class="px-6 py-4 text-center border-r border-slate-200" style="min-width: 130px; width: 130px;">
                          <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 shadow-sm">
                            {{ $job->product_category ?? 'Uncategorized' }}
                          </span>
                        </td>
                        <td class="px-6 py-4 text-center border-r border-slate-200" style="min-width: 140px; width: 140px;">
                          <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold shadow-sm
                            {{ ($job->location ?? $job->platform ?? 'N/A') === 'N/A' ? 'bg-slate-100 text-slate-600' : 'bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700' }}">
                            {{ $job->location ?? $job->platform ?? 'N/A' }}
                          </span>
                        </td>
                        <td class="px-6 py-4 text-center border-r border-slate-200" style="min-width: 110px; width: 110px;">
                          <div class="bg-green-50 rounded-xl px-3 py-2 inline-block">
                            <div class="text-sm font-bold text-green-700">
                              {{ $job->date?->format('d M') ?? 'TBD' }}
                            </div>
                          </div>
                        </td>
                        <td class="px-6 py-4 text-center relative group border-r border-slate-200" style="min-width:110px;width:110px;">
                          @php
                              // FIXED: Use $job-> instead of $job[]
                              $endRaw = $job->date_finish ?? $job->end_date ?? null;
                              $endDate = $endRaw
                                          ? ($endRaw instanceof \Illuminate\Support\Carbon
                                                ? $endRaw
                                                : \Illuminate\Support\Carbon::parse($endRaw))
                                          : null;

                              $today = now();

                              // Defaults
                              $status      = 'upcoming';
                              $statusColor = 'text-blue-600 bg-blue-50';
                              $tooltipText = 'Upcoming deadline';

                              // Check for completed status - FIXED: Use $job-> syntax
                              $statusText = strtolower((string) ($job->status ?? $job->project_status ?? ''));
                              if (in_array($statusText, ['completed','done','finished','complete'])) {
                                  $status      = 'completed';
                                  $statusColor = 'text-green-700 bg-green-50';
                                  $tooltipText = 'Project completed';
                              } elseif ($endDate) {
                                  // Calculate days until deadline
                                  $daysUntil = $today->diffInDays($endDate, false);
                                  if ($daysUntil < 0) {
                                      $status      = 'overdue';
                                      $statusColor = 'text-red-700 bg-red-50';
                                      $tooltipText = 'Project overdue by ' . abs($daysUntil) . ' day(s)';
                                  } elseif ($daysUntil <= 7) {
                                      $status      = 'urgent';
                                      $statusColor = 'text-amber-700 bg-amber-50';
                                      $tooltipText = 'Deadline in ' . $daysUntil . ' day(s)';
                                  } else {
                                      $tooltipText = 'Deadline in ' . $daysUntil . ' day(s)';
                                  }
                              }
                          @endphp
                          <div class="rounded-xl px-3 py-2 inline-flex items-center space-x-2 {{ $statusColor }}">
                            <div class="text-sm font-bold">
                              {{ $endDate?->format('d M') ?? 'TBD' }}
                            </div>
                            @if($endDate)
                              <div class="flex items-center">
                                @if($status === 'completed')
                                  <span class="text-green-500 text-base">✓</span>
                                @elseif($status === 'urgent')
                                  <span class="text-amber-500 text-base">⚠</span>
                                @elseif($status === 'overdue')
                                  <span class="text-red-500 text-base">!</span>
                                @endif
                              </div>
                            @endif
                          </div>
                          @if($endDate)
                            <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-3 px-3 py-2 text-xs font-medium text-white bg-slate-800 rounded-xl opacity-0 group-hover:opacity-100 transition-all duration-300 pointer-events-none whitespace-nowrap z-30 shadow-lg">
                              {{ $tooltipText }}
                              <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-slate-800"></div>
                            </div>
                          @endif
                        </td>
                        @foreach(['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'] as $m)
                          <td class="px-4 py-4 text-center bg-gradient-to-b from-blue-50/30 to-indigo-50/30 hover:from-blue-100/50 hover:to-indigo-100/50 transition-all duration-300 border-r border-blue-200/40" style="min-width: 95px; width: 95px;">
                            @php
                                // FIXED: Use object property access with dynamic properties
                                $monthValue = $job->{"check_$m"} ??
                                             $job->{"month_$m"} ??
                                             $job->{"{$m}_status"} ??
                                             ($job->month === $m ? ($job->status ?? 'Active') : '');

                                // If still empty, check if this month falls within the project timeline
                                if (empty($monthValue) && $job->date && $endDate) {
                                    $monthNum = array_search($m, ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec']) + 1;
                                    $currentYear = $job->date->year;
                                    $monthStart = \Carbon\Carbon::create($currentYear, $monthNum, 1);
                                    $monthEnd = $monthStart->copy()->endOfMonth();

                                    // Check if project runs during this month
                                    if ($job->date <= $monthEnd && $endDate >= $monthStart) {
                                        $monthValue = 'Active';
                                    }
                                }
                            @endphp
                            <input
                                type="text"
                                value="{{ $monthValue }}"
                                data-id="{{ $job->id }}"
                                data-type="master"
                                data-field="check_{{ $m }}"
                                onblur="autoSave(this)"
                                class="w-full bg-white/80 border border-blue-200 text-center text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white focus:shadow-lg px-2 py-2 rounded-xl transition-all duration-200 placeholder-slate-400 hover:bg-white hover:shadow-md"
                                placeholder="•"
                                style="min-width: 75px;"
                            />
                          </td>
                        @endforeach
                        <td class="px-6 py-4" style="min-width: 220px; width: 220px;">
                          <input
                              type="text"
                              value="{{ $job->remarks ?? '' }}"
                              data-id="{{ $job->id }}"
                              data-type="master"
                              data-field="remarks"
                              onblur="autoSave(this)"
                              class="w-full bg-slate-50 border border-slate-200 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400 focus:bg-white focus:shadow-lg px-4 py-3 rounded-xl transition-all duration-200 placeholder-slate-400 hover:bg-white hover:shadow-md"
                              placeholder="Add remarks..."
                              style="min-width: 200px;"
                          />
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>

            <!-- Enhanced Custom Styles -->
            <style>
              /* Sticky column positioning */
              .sticky.left-140 {
                left: 140px;
              }

              /* Custom scrollbar for the table */
              .custom-scrollbar::-webkit-scrollbar {
                height: 12px;
              }

              .custom-scrollbar::-webkit-scrollbar-track {
                background: linear-gradient(to right, #f8fafc, #f1f5f9);
                border-radius: 8px;
                box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
              }

              .custom-scrollbar::-webkit-scrollbar-thumb {
                background: linear-gradient(to right, #e2e8f0, #cbd5e1);
                border-radius: 8px;
                border: 2px solid #f8fafc;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
              }

              .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(to right, #cbd5e1, #94a3b8);
              }

              /* Enhanced transitions and animations */
              * {
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
              }

              /* Input field enhancements */
              input:focus {
                transform: translateY(-1px);
                box-shadow: 0 8px 25px rgba(59, 130, 246, 0.15);
              }

              input:hover {
                transform: translateY(-0.5px);
              }

              /* Row hover effects */
              tbody tr:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
              }

              /* Button hover effects */
              button:hover {
                transform: translateY(-2px);
              }

              /* Smooth fade-in animation */
              @keyframes slideInUp {
                from {
                  opacity: 0;
                  transform: translateY(30px);
                }
                to {
                  opacity: 1;
                  transform: translateY(0);
                }
              }

              section {
                animation: slideInUp 0.6s ease-out;
              }

              /* Enhanced gradient backgrounds */
              .bg-gradient-to-br {
                background-image: linear-gradient(to bottom right, var(--tw-gradient-stops));
              }

              .bg-gradient-to-r {
                background-image: linear-gradient(to right, var(--tw-gradient-stops));
              }

              .bg-gradient-to-b {
                background-image: linear-gradient(to bottom, var(--tw-gradient-stops));
              }

              /* Premium shadow effects */
              .shadow-sm {
                box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px 0 rgba(0, 0, 0, 0.04);
              }

              .shadow-lg {
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
              }

              .shadow-xl {
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
              }

              .shadow-2xl {
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
              }

              /* Enhanced hover effects with smooth transitions */
              .hover\:shadow-md:hover {
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
              }

              .hover\:shadow-xl:hover {
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
              }

              /* Backdrop blur effects for modern glass morphism */
              .backdrop-blur-sm {
                backdrop-filter: blur(4px);
              }

              .backdrop-blur-md {
                backdrop-filter: blur(8px);
              }

              /* Custom month input styling */
              input[data-field*="check_"] {
                background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(248,250,252,0.9));
                border: 1.5px solid rgba(59, 130, 246, 0.2);
              }

              input[data-field*="check_"]:focus {
                background: linear-gradient(135deg, #ffffff, #f8fafc);
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1), 0 8px 25px rgba(59, 130, 246, 0.15);
              }

              input[data-field*="check_"]:hover {
                background: linear-gradient(135deg, #ffffff, #f1f5f9);
                border-color: rgba(59, 130, 246, 0.4);
              }

              /* Remarks input styling */
              input[data-field="remarks"] {
                background: linear-gradient(135deg, rgba(248,250,252,0.8), rgba(241,245,249,0.8));
              }

              input[data-field="remarks"]:focus {
                background: linear-gradient(135deg, #ffffff, #f8fafc);
              }

              /* Select dropdown enhancements */
              select {
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
                background-position: right 12px center;
                background-repeat: no-repeat;
                background-size: 16px;
                padding-right: 40px;
              }

              select:focus {
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%233b82f6' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
              }

              /* Responsive design enhancements */
              @media (max-width: 768px) {
                .sticky.left-140 {
                  left: 120px;
                }

                th, td {
                  min-width: 120px !important;
                  width: 120px !important;
                }

                .sticky[style*="130px"] {
                  min-width: 120px !important;
                  width: 120px !important;
                }

                .sticky[style*="240px"] {
                  min-width: 200px !important;
                  width: 200px !important;
                }
              }

              @media (max-width: 640px) {
                .overflow-x-auto {
                  border-radius: 16px;
                }

                th, td {
                  padding-left: 12px;
                  padding-right: 12px;
                }
              }

              /* Loading and interaction states */
              .loading {
                opacity: 0.7;
                pointer-events: none;
              }

              /* Enhanced focus rings */
              .focus-ring:focus {
                outline: none;
                ring: 2px;
                ring-color: rgb(59 130 246);
                ring-offset: 2px;
              }

              /* Tooltip improvements */
              .tooltip {
                position: relative;
              }

              .tooltip::before {
                content: attr(data-tooltip);
                position: absolute;
                bottom: 100%;
                left: 50%;
                transform: translateX(-50%);
                background: rgba(15, 23, 42, 0.9);
                color: white;
                padding: 8px 12px;
                border-radius: 8px;
                font-size: 12px;
                font-weight: 500;
                white-space: nowrap;
                opacity: 0;
                pointer-events: none;
                transition: all 0.2s ease;
                z-index: 1000;
              }

              .tooltip:hover::before {
                opacity: 1;
                transform: translateX(-50%) translateY(-4px);
              }

              /* Status indicator animations */
              .animate-pulse-slow {
                animation: pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
              }

              @keyframes pulse {
                0%, 100% {
                  opacity: 1;
                }
                50% {
                  opacity: 0.7;
                }
              }

              /* Modern card styling */
              .card-modern {
                background: linear-gradient(145deg, #ffffff, #f8fafc);
                border: 1px solid rgba(226, 232, 240, 0.8);
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05), 0 1px 2px rgba(0, 0, 0, 0.1);
              }

              .card-modern:hover {
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07), 0 2px 4px rgba(0, 0, 0, 0.06);
              }

              /* Auto-save feedback */
              .saving {
                border-color: #f59e0b !important;
                background: linear-gradient(135deg, #fef3c7, #fde68a) !important;
              }

              .saved {
                border-color: #10b981 !important;
                background: linear-gradient(135deg, #d1fae5, #a7f3d0) !important;
              }

              .error {
                border-color: #ef4444 !important;
                background: linear-gradient(135deg, #fecaca, #fca5a5) !important;
              }
            </style>

            <!-- Add JavaScript for auto-save functionality -->
            <script>
              // Auto-save function with visual feedback
              async function autoSave(input) {
                const originalClass = input.className;

                try {
                  // Add saving state
                  input.classList.add('saving');

                  const response = await fetch('/api/update-job', {
                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                      id: input.dataset.id,
                      type: input.dataset.type,
                      field: input.dataset.field,
                      value: input.value
                    })
                  });

                  if (response.ok) {
                    // Success feedback
                    input.className = originalClass;
                    input.classList.add('saved');
                    setTimeout(() => {
                      input.className = originalClass;
                    }, 1500);
                  } else {
                    throw new Error('Save failed');
                  }
                } catch (error) {
                  // Error feedback
                  input.className = originalClass;
                  input.classList.add('error');
                  setTimeout(() => {
                    input.className = originalClass;
                  }, 2000);
                  console.error('Auto-save error:', error);
                }
              }

              // Enhanced UX: Save on Enter key
              document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.matches('input[data-field]')) {
                  e.target.blur(); // Trigger the onblur auto-save
                }
              });

              // Auto-populate month fields based on project timeline
              document.addEventListener('DOMContentLoaded', function() {
                const rows = document.querySelectorAll('tbody tr');

                rows.forEach(row => {
                  const startDateCell = row.querySelector('td:nth-child(6) .text-green-700');
                  const endDateCell = row.querySelector('td:nth-child(7) .text-sm');

                  if (startDateCell && endDateCell) {
                    const monthInputs = row.querySelectorAll('input[data-field*="check_"]');

                    monthInputs.forEach(input => {
                      if (!input.value.trim()) {
                        // Auto-populate based on project timeline
                        const month = input.dataset.field.replace('check_', '');
                        const monthIndex = ['jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec'].indexOf(month);

                        // Simple logic: if project spans multiple months, mark as "Active"
                        if (monthIndex >= 0) {
                          input.placeholder = "Active";
                          input.style.opacity = "0.7";
                        }
                      }
                    });
                  }
                });
              });
            </script>
          @endif
        </section>
      </div>
    </main>
  </div>
</x-app-layout>
