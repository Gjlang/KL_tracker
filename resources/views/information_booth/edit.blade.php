@extends('layouts.app')

@section('content')
<div class="w-screen min-h-screen bg-[#F7F7F9] p-6">
  <div class="max-w-4xl mx-auto space-y-8">

    {{-- Header --}}
    <div class="flex items-center justify-between">
      <h1 class="text-4xl font-serif font-bold text-[#1C1E26] tracking-tight">Edit Backlog Entry</h1>
      <a href="{{ route('information.booth.index') }}"
         class="btn-ghost inline-flex items-center gap-2 transition-all duration-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Back to List
      </a>
    </div>

    {{-- Errors --}}
    @if ($errors->any())
      <div class="rounded-2xl border border-red-200 bg-red-50 px-6 py-4">
        <div class="flex items-start gap-3">
          <svg class="w-5 h-5 text-[#d33831] mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
          </svg>
          <div>
            <h3 class="text-sm font-medium text-[#d33831] mb-1">Please correct the following errors:</h3>
            <ul class="text-sm text-red-700 space-y-1">
              @foreach ($errors->all() as $e)
                <li class="flex items-center gap-2">
                  <span class="w-1 h-1 bg-red-400 rounded-full flex-shrink-0"></span>
                  {{ $e }}
                </li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>
    @endif

    {{-- Form Card --}}
    <div class="card p-8" x-data="{ isSubmitting: false }">
      <form action="{{ route('information.booth.update', $feed->id) }}"
            method="POST"
            class="space-y-8"
            @submit="isSubmitting = true">
        @csrf
        @method('PUT')

        {{-- Basic Information Section --}}
        <div class="space-y-6">
          <h2 class="text-lg font-serif font-semibold text-[#1C1E26] border-b border-neutral-200 pb-2">
            Basic Information
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Date --}}
            <div class="space-y-2">
              <label class="label block">Date</label>
              <div class="relative">
                <input type="date" name="date"
                       value="{{ old('date', optional($feed->date)->format('Y-m-d')) }}"
                       class="input w-full @error('date') border-red-300 focus:ring-red-500 @enderror">
                <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-neutral-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
              @error('date')
                <p class="text-xs text-[#d33831] mt-1">{{ $message }}</p>
              @enderror
            </div>

            {{-- Expected Finish --}}
            <div class="space-y-2">
              <label class="label block">Expected Finish</label>
              <div class="relative">
                <input type="date" name="expected_finish_date"
                       value="{{ old('expected_finish_date', optional($feed->expected_finish_date)->format('Y-m-d')) }}"
                       class="input w-full @error('expected_finish_date') border-red-300 focus:ring-red-500 @enderror">
                <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-neutral-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
              @error('expected_finish_date')
                <p class="text-xs text-[#d33831] mt-1">{{ $message }}</p>
              @enderror
            </div>
          </div>
        </div>

        {{-- Project Details Section --}}
        <div class="space-y-6">
          <h2 class="text-lg font-serif font-semibold text-[#1C1E26] border-b border-neutral-200 pb-2">
            Project Details
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Company --}}
            <div class="space-y-2">
              <label class="label block">Company</label>
              <input type="text" name="company"
                     value="{{ old('company', $feed->company) }}"
                     placeholder="Enter company name"
                     class="input w-full @error('company') border-red-300 focus:ring-red-500 @enderror">
              @error('company')
                <p class="text-xs text-[#d33831] mt-1">{{ $message }}</p>
              @enderror
            </div>

            {{-- Client --}}
            <div class="space-y-2">
              <label class="label block">Client</label>
              <input type="text" name="client"
                     value="{{ old('client', $feed->client) }}"
                     placeholder="Enter client name"
                     class="input w-full @error('client') border-red-300 focus:ring-red-500 @enderror">
              @error('client')
                <p class="text-xs text-[#d33831] mt-1">{{ $message }}</p>
              @enderror
            </div>

            {{-- Product --}}
            <div class="space-y-2">
              <label class="label block">Product</label>
              <input type="text" name="product"
                     value="{{ old('product', $feed->product) }}"
                     placeholder="Enter product name"
                     class="input w-full @error('product') border-red-300 focus:ring-red-500 @enderror">
              @error('product')
                <p class="text-xs text-[#d33831] mt-1">{{ $message }}</p>
              @enderror
            </div>

            {{-- Servicing --}}
            <div class="space-y-2">
              <label class="label block">Servicing</label>
              <input type="text" name="servicing"
                     value="{{ old('servicing', $feed->servicing) }}"
                     placeholder="Enter service type"
                     class="input w-full @error('servicing') border-red-300 focus:ring-red-500 @enderror">
              @error('servicing')
                <p class="text-xs text-[#d33831] mt-1">{{ $message }}</p>
              @enderror
            </div>

            {{-- Location --}}
            <div class="space-y-2 md:col-span-2">
              <label class="label block">Location</label>
              <input type="text" name="location"
                     value="{{ old('location', $feed->location) }}"
                     placeholder="Enter service location"
                     class="input w-full @error('location') border-red-300 focus:ring-red-500 @enderror">
              @error('location')
                <p class="text-xs text-[#d33831] mt-1">{{ $message }}</p>
              @enderror
            </div>
          </div>
        </div>

        {{-- Status & Assignment Section --}}
        <div class="space-y-6">
          <h2 class="text-lg font-serif font-semibold text-[#1C1E26] border-b border-neutral-200 pb-2">
            Status & Assignment
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Status --}}
            <div class="space-y-2">
              <label class="label block">Status</label>
              @php $current = old('status', $feed->status); @endphp
              <div class="relative">
                <select name="status" class="input w-full pr-10 @error('status') border-red-300 focus:ring-red-500 @enderror">
                  <option value="pending"     @selected($current==='pending')>Pending</option>
                  <option value="in-progress" @selected($current==='in-progress')>In Progress</option>
                  <option value="completed"        @selected($current==='completed')>Completed</option>
                  <option value="cancelled"   @selected($current==='cancelled')>Cancelled</option>
                </select>
                <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-neutral-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
              </div>
              @error('status')
                <p class="text-xs text-[#d33831] mt-1">{{ $message }}</p>
              @enderror
            </div>

            {{-- Attended By --}}
            <div class="space-y-2">
              <label class="label block">Attended By</label>
              <input type="text" name="attended_by"
                     value="{{ old('attended_by', $feed->attended_by) }}"
                     placeholder="Enter PIC or team member"
                     class="input w-full @error('attended_by') border-red-300 focus:ring-red-500 @enderror">
              @error('attended_by')
                <p class="text-xs text-[#d33831] mt-1">{{ $message }}</p>
              @enderror
            </div>
          </div>
        </div>

        {{-- Notes Section --}}
        <div class="space-y-6">
          <h2 class="text-lg font-serif font-semibold text-[#1C1E26] border-b border-neutral-200 pb-2">
            Additional Notes
          </h2>

          <div class="space-y-2">
            <label class="label block">Reasons</label>
            <textarea name="reasons" rows="4"
                      placeholder="Enter notes, follow-up reasons, or additional details..."
                      class="input w-full min-h-[120px] resize-y @error('reasons') border-red-300 focus:ring-red-500 @enderror">{{ old('reasons', $feed->reasons) }}</textarea>
            @error('reasons')
              <p class="text-xs text-[#d33831] mt-1">{{ $message }}</p>
            @enderror
          </div>
        </div>

        {{-- Form Actions --}}
        <div class="pt-6 border-t border-neutral-200">
          <div class="flex flex-col sm:flex-row gap-3 sm:justify-end">
            <a href="{{ route('information.booth.index') }}"
               class="btn-ghost text-center sm:text-left order-2 sm:order-1">
              Cancel
            </a>
            <button type="submit"
                    class="btn-primary order-1 sm:order-2 relative overflow-hidden"
                    :disabled="isSubmitting">
              <span x-show="!isSubmitting" class="flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Update Entry
              </span>
              <span x-show="isSubmitting" class="flex items-center justify-center gap-2">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Updating...
              </span>
            </button>
          </div>
        </div>
      </form>
    </div>

  </div>
</div>
@endsection
