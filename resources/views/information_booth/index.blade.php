@extends('layouts.app')

@section('content')
@php use Illuminate\Support\Str; @endphp

<div class="min-h-screen bg-paper">
    {{-- Page Header --}}
    <header class="bg-white border-b border-hairline sticky top-0 z-50">
        <div class="mx-auto max-w-7xl px-6 py-6">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-serif font-medium tracking-wide text-ink">Information Hub</h1>
                <nav class="text-sm font-sans text-neutral-600 tracking-wider">
                    Calendar + Client Feed Backlog
                </nav>
            </div>
        </div>
    </header>

    <div class="mx-auto max-w-7xl px-6 py-8 space-y-8">
        {{-- Success/Error Messages --}}
        @if(session('ok'))
            <div class="card border-l-4 border-green-500 bg-green-50/50 text-green-800 font-sans text-sm leading-relaxed">
                <div class="p-4">
                    ✓ {{ session('ok') }}
                </div>
            </div>
        @endif
        @if($errors->any())
            <div class="card border-l-4 border-red-500 bg-red-50/50 text-red-800 font-sans text-sm leading-relaxed">
                <div class="p-4">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        {{-- Layout: Calendar (left) + Backlog (right) --}}
        <div class="grid grid-cols-1 xl:grid-cols-4 gap-8">
            {{-- Calendar Column --}}
            <div class="p-6">
  @include('calendar._fullcalendar_embed')
</div>


            {{-- Backlog Section --}}
            <div class="xl:col-span-3 space-y-8">
                {{-- Add Backlog Form --}}
                <div class="card">
                    <div class="px-6 py-5 border-b border-hairline">
                        <h2 class="text-xl font-serif font-medium text-ink tracking-wide">Add Backlog Entry</h2>
                    </div>

                    <form method="POST" action="{{ route('information.booth.feeds.store') }}" class="p-6">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div class="form-group">
                                <label for="date" class="form-label">Date</label>
                                <input type="date"
                                       id="date"
                                       name="date"
                                       class="form-input"
                                       required>
                            </div>

                            <div class="form-group">
                            <label for="expected_finish_date" class="form-label">Expected Finish Date</label>
                            <input type="date"
                                    id="expected_finish_date"
                                    name="expected_finish_date"
                                    class="form-input"
                                    value="{{ old('expected_finish_date') }}">
                            </div>

                            <div class="form-group">
                            <label for="company" class="form-label">Company</label>
                            <input type="text"
                                    id="company"
                                    name="company"
                                    class="form-input"
                                    placeholder="Company name"
                                    value="{{ old('company') }}">
                            </div>

                            <div class="form-group">
                                <label for="client" class="form-label">Person In Charge</label>
                                <input type="text"
                                       id="client"
                                       name="client"
                                       class="form-input"
                                       placeholder="Client name"
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="status" class="form-label">Status</label>
                                <select id="status" name="status" class="form-input">
                                    <option value="pending">Pending</option>
                                    <option value="in-progress">In Progress</option>
                                    <option value="done">Done</option>
                                    <option value="cancelled">Cancelled</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="servicing" class="form-label">Servicing</label>
                                <input type="text"
                                       id="servicing"
                                       name="servicing"
                                       class="form-input"
                                       placeholder="Service type">
                            </div>

                            <div class="form-group">
                                <label for="product" class="form-label">Product</label>
                                <input type="text"
                                       id="product"
                                       name="product"
                                       class="form-input"
                                       placeholder="Product name">
                            </div>

                            <div class="form-group">
                                <label for="location" class="form-label">Location</label>
                                <input type="text"
                                       id="location"
                                       name="location"
                                       class="form-input"
                                       placeholder="Service location">
                            </div>

                            <div class="form-group lg:col-span-2">
                                <label for="attended_by" class="form-label">Attended By</label>
                                <input type="text"
                                       id="attended_by"
                                       name="attended_by"
                                       class="form-input"
                                       placeholder="PIC / Team member">
                            </div>

                            <div class="form-group lg:col-span-1">
                                <label for="reasons" class="form-label">Reasons</label>
                                <input type="text"
                                       id="reasons"
                                       name="reasons"
                                       class="form-input"
                                       placeholder="Notes / follow-up reasons">
                            </div>
                        </div>

                        {{-- Sticky Save Action --}}
                        <div class="sticky bottom-0 bg-white/95 backdrop-blur-sm border-t border-hairline mt-8 -mx-6 px-6 py-4">
                            <div class="flex justify-end">
                                <button type="submit" class="btn-primary">
                                    Save Entry
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Filters Bar --}}
                <div class="filters-sticky" x-data="{ isSticky: false }"
                     x-init="() => {
                         const observer = new IntersectionObserver(
                             ([e]) => isSticky = e.intersectionRatio < 1,
                             { threshold: [1] }
                         );
                         observer.observe($el);
                     }"
                     :class="{ 'shadow-lg': isSticky }">
                    <div class="card">
                        <form method="GET" class="px-6 py-5">
                            <div class="flex flex-wrap items-end gap-4">
                                <div class="flex-1 min-w-64">
                                    <label for="filter_client" class="form-label text-xs">Search Client</label>
                                    <input id="filter_client"
                                           name="client"
                                           value="{{ $filters['client'] ?? '' }}"
                                           placeholder="Enter client name..."
                                           class="form-input text-sm h-10">
                                </div>
                                <div class="w-40">
                                    <label for="filter_status" class="form-label text-xs">Status</label>
                                    <select id="filter_status" name="status" class="form-input text-sm h-10">
                                        <option value="">All Status</option>
                                        @foreach(['pending','in-progress','done','cancelled'] as $st)
                                            <option value="{{ $st }}" @selected(($filters['status'] ?? '') === $st)>
                                                {{ ucfirst(str_replace('-', ' ', $st)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn-ghost h-10">
                                    Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Backlog Table --}}
                <div class="card">
                    <div class="px-6 py-5 border-b border-hairline">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-serif font-medium text-ink tracking-wide">Client Feed Backlog</h2>
                            <div class="text-sm font-sans text-neutral-600">
                                {{ $feeds->count() }} Records
                                @if(isset($filters['status']) && $filters['status'])
                                    • {{ ucfirst(str_replace('-', ' ', $filters['status'])) }}
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($feeds->count() > 0)
                        {{-- Desktop Table --}}
                        <div class="hidden lg:block overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-neutral-50/50 border-b border-hairline">
                                    <tr>
                                        <th class="table-header">Servicing</th>
                                        <th class="table-header">Product</th>
                                        <th class="table-header">Location</th>
                                        <th class="table-header">Company</th>
                                        <th class="table-header">Person In Charge</th>
                                        <th class="table-header">Date</th>
                                        <th class="table-header">Expected Date Finish</th>
                                        <th class="table-header">Status</th>
                                        <th class="table-header">Attended By</th>
                                        <th class="table-header">Reasons</th>
                                        <th class="table-header w-20">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-hairline">
                                    @foreach($feeds as $f)
                                        <tr class="table-row">
                                            {{-- Servicing --}}
                                            <td class="table-cell" title="{{ $f->servicing }}">
                                                {{ Str::limit($f->servicing, 15) }}
                                            </td>

                                            {{-- Product --}}
                                            <td class="table-cell" title="{{ $f->product }}">
                                                {{ Str::limit($f->product, 15) }}
                                            </td>

                                            {{-- Location --}}
                                            <td class="table-cell" title="{{ $f->location }}">
                                                {{ Str::limit($f->location, 15) }}
                                            </td>

                                            {{-- Company --}}
                                            <td class="table-cell font-medium" title="{{ $f->company }}">
                                                {{ Str::limit($f->company ?? '—', 20) }}
                                            </td>

                                            {{-- Person In Charge (Client) --}}
                                            <td class="table-cell font-medium" title="{{ $f->client }}">
                                                {{ Str::limit($f->client, 20) }}
                                            </td>

                                            {{-- Date --}}
                                            <td class="table-cell" title="{{ optional($f->date)->format('d/m/Y') }}">
                                                {{ optional($f->date)->format('d/m/Y') }}
                                            </td>

                                            {{-- Expected Date Finish --}}
                                            <td class="table-cell" title="{{ optional($f->expected_finish_date)->format('d/m/Y') }}">
                                                {{ optional($f->expected_finish_date)->format('d/m/Y') ?? '—' }}
                                            </td>

                                            {{-- Status --}}
                                            <td class="table-cell">
                                                @php
                                                    $statusConfig = match($f->status) {
                                                        'pending' => ['text' => 'Pending', 'class' => 'status-pending'],
                                                        'in-progress' => ['text' => 'In Progress', 'class' => 'status-progress'],
                                                        'done' => ['text' => 'Done', 'class' => 'status-done'],
                                                        'cancelled' => ['text' => 'Cancelled', 'class' => 'status-cancelled'],
                                                        default => ['text' => 'Pending', 'class' => 'status-pending'],
                                                    };
                                                @endphp
                                                <span class="status-chip {{ $statusConfig['class'] }}">
                                                    {{ $statusConfig['text'] }}
                                                </span>
                                            </td>

                                            {{-- Attended By --}}
                                            <td class="table-cell" title="{{ $f->attended_by }}">
                                                {{ Str::limit($f->attended_by ?? '', 15) }}
                                            </td>

                                            {{-- Reasons --}}
                                            <td class="table-cell text-neutral-600" title="{{ $f->reasons }}">
                                                {{ Str::limit($f->reasons ?? '', 25) }}
                                            </td>

                                            {{-- Actions --}}
                                            <td class="table-cell">
                                                <form method="POST"
                                                    action="{{ route('information.booth.feeds.destroy', $f) }}"
                                                    x-data
                                                    @submit.prevent="if(confirm('Are you sure you want to delete this entry?')) $el.submit()"
                                                    class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit"
                                                            class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors duration-150"
                                                            title="Delete Entry">
                                                        Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{-- Mobile Cards --}}
                        <div class="lg:hidden divide-y divide-hairline">
                            @foreach($feeds as $f)
                                <div class="p-6 hover:bg-neutral-50/50 transition-colors duration-150">
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <div class="font-medium text-ink">{{ $f->client }}</div>
                                            <div class="text-sm text-neutral-600">{{ optional($f->date)->format('M d, Y') }}</div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            @php
                                                $statusConfig = match($f->status) {
                                                    'pending' => ['text' => 'Pending', 'class' => 'status-pending'],
                                                    'in-progress' => ['text' => 'In Progress', 'class' => 'status-progress'],
                                                    'done' => ['text' => 'Done', 'class' => 'status-done'],
                                                    'cancelled' => ['text' => 'Cancelled', 'class' => 'status-cancelled'],
                                                    default => ['text' => 'Pending', 'class' => 'status-pending']
                                                };
                                            @endphp
                                            <span class="status-chip {{ $statusConfig['class'] }}">
                                                {{ $statusConfig['text'] }}
                                            </span>
                                            <form method="POST"
                                                  action="{{ route('information.booth.feeds.destroy', $f) }}"
                                                  x-data
                                                  @submit.prevent="if(confirm('Are you sure you want to delete this entry?')) $el.submit()"
                                                  class="inline">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                        class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors duration-150">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                                        @if($f->servicing)
                                            <div>
                                                <dt class="text-neutral-600 font-medium">Servicing</dt>
                                                <dd class="text-ink">{{ $f->servicing }}</dd>
                                            </div>
                                        @endif
                                        @if($f->product)
                                            <div>
                                                <dt class="text-neutral-600 font-medium">Product</dt>
                                                <dd class="text-ink">{{ $f->product }}</dd>
                                            </div>
                                        @endif
                                        @if($f->location)
                                            <div>
                                                <dt class="text-neutral-600 font-medium">Location</dt>
                                                <dd class="text-ink">{{ $f->location }}</dd>
                                            </div>
                                        @endif
                                        @if($f->attended_by)
                                            <div>
                                                <dt class="text-neutral-600 font-medium">Attended By</dt>
                                                <dd class="text-ink">{{ $f->attended_by }}</dd>
                                            </div>
                                        @endif
                                        @if($f->reasons)
                                            <div class="col-span-2">
                                                <dt class="text-neutral-600 font-medium">Reasons</dt>
                                                <dd class="text-ink">{{ $f->reasons }}</dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Empty State --}}
                        <div class="px-6 py-20 text-center">
                            <div class="mx-auto w-24 h-24 bg-neutral-100 rounded-full flex items-center justify-center mb-8">
                                <svg class="w-12 h-12 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-serif font-medium text-ink mb-2">No backlog entries yet</h3>
                            <p class="text-neutral-600 mb-8 max-w-sm mx-auto leading-relaxed">
                                Get started by adding your first client feed entry using the form above.
                            </p>
                            <button onclick="document.querySelector('#client').focus()"
                                    class="btn-ghost">
                                Add First Entry
                            </button>
                        </div>
                    @endif

                    {{-- Pagination --}}
                    @if($feeds->hasPages())
                        <div class="px-6 py-4 border-t border-hairline">
                            {{ $feeds->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- All CSS and JavaScript in one file --}}
<style>
/* Import elegant fonts */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:wght@400;500;600&display=swap');

/* Custom CSS Variables */
:root {
    --color-paper: #F7F7F9;
    --color-white: #FFFFFF;
    --color-ink: #1C1E26;
    --color-hairline: #EAEAEA;
    --color-primary: #22255b;
    --color-primary-hover: #1a1d4a;
    --color-accent: #4bbbed;
    --color-destructive: #d33831;
}

/* Base styles */
.bg-paper {
    background-color: var(--color-paper);
}

.text-ink {
    color: var(--color-ink);
}

.border-hairline {
    border-color: var(--color-hairline);
}

.divide-hairline > :not([hidden]) ~ :not([hidden]) {
    border-top-color: var(--color-hairline);
}

/* Typography */
.font-serif {
    font-family: 'Playfair Display', serif;
}

.font-sans {
    font-family: 'Inter', sans-serif;
}

/* Card component */
.card {
    background-color: var(--color-white);
    border-radius: 1rem;
    border: 1px solid #e5e5e5;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
}

/* Form components */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 500;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    font-family: 'Inter', sans-serif;
}

.form-input {
    display: block;
    width: 100%;
    height: 2.75rem;
    padding: 0.625rem 1rem;
    background-color: var(--color-white);
    border: 1px solid #d1d5db;
    border-radius: 0.75rem;
    font-size: 0.875rem;
    font-family: 'Inter', sans-serif;
    color: var(--color-ink);
    transition: all 0.15s ease;
}

.form-input::placeholder {
    color: #9ca3af;
}

.form-input:hover {
    border-color: #9ca3af;
}

.form-input:focus {
    outline: none;
    border-color: transparent;
    box-shadow: 0 0 0 2px var(--color-accent);
}

/* Button components */
.btn-primary {
    display: inline-flex;
    align-items: center;
    padding: 0.625rem 1.5rem;
    background-color: var(--color-primary);
    color: var(--color-white);
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 9999px;
    font-family: 'Inter', sans-serif;
    letter-spacing: 0.025em;
    transition: all 0.15s ease;
    border: none;
    cursor: pointer;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.btn-primary:hover {
    opacity: 0.9;
}

.btn-primary:focus {
    outline: 2px solid var(--color-primary);
    outline-offset: 2px;
}

.btn-ghost {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    border: 1px solid #d1d5db;
    color: #374151;
    font-size: 0.875rem;
    font-weight: 500;
    border-radius: 0.5rem;
    font-family: 'Inter', sans-serif;
    transition: all 0.15s ease;
    background: transparent;
    cursor: pointer;
}

.btn-ghost:hover {
    background-color: #f9fafb;
    border-color: #9ca3af;
}

.btn-ghost:focus {
    outline: 2px solid #6b7280;
    outline-offset: 1px;
}

/* Filters sticky behavior */
.filters-sticky {
    position: sticky;
    top: 6rem;
    z-index: 40;
    transition: box-shadow 0.2s ease;
}

/* Table components */
.table-header {
    padding: 1rem 1.5rem;
    text-align: left;
    font-size: 0.75rem;
    font-weight: 500;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    font-family: 'Inter', sans-serif;
}

.table-cell {
    padding: 1rem 1.5rem;
    white-space: nowrap;
    font-size: 0.875rem;
    font-family: 'Inter', sans-serif;
}

.table-row {
    transition: all 0.15s ease;
}

.table-row:hover {
    background-color: rgba(249, 250, 251, 0.8);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.table-row:hover .table-cell {
    color: var(--color-ink);
}

/* Status chips */
.status-chip {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.625rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
    font-family: 'Inter', sans-serif;
    letter-spacing: 0.025em;
}

.status-pending {
    background-color: #f3f4f6;
    color: #374151;
}

.status-progress {
    background-color: #E9F6FB;
    color: #1e40af;
}

.status-done {
    background-color: #EAF7EE;
    color: #059669;
}

.status-cancelled {
    background-color: var(--color-destructive);
    color: var(--color-white);
}

/* Focus styles for accessibility */
button:focus,
input:focus,
select:focus {
    outline: 2px solid var(--color-accent);
    outline-offset: 2px;
}

/* Mobile responsive adjustments */
@media (max-width: 768px) {
    .form-input {
        height: 3rem;
        font-size: 1rem;
    }

    .btn-primary,
    .btn-ghost {
        height: 2.75rem;
        font-size: 1rem;
    }
}

/* Smooth transitions for interactive elements */
* {
    transition-property: color, background-color, border-color;
    transition-duration: 150ms;
}

/* Custom scrollbar for webkit browsers */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: var(--color-paper);
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Utility classes for better spacing */
.space-y-8 > :not([hidden]) ~ :not([hidden]) {
    margin-top: 2rem;
}

.space-y-6 > :not([hidden]) ~ :not([hidden]) {
    margin-top: 1.5rem;
}

.space-y-4 > :not([hidden]) ~ :not([hidden]) {
    margin-top: 1rem;
}

.space-y-2 > :not([hidden]) ~ :not([hidden]) {
    margin-top: 0.5rem;
}

.gap-8 {
    gap: 2rem;
}

.gap-6 {
    gap: 1.5rem;
}

.gap-4 {
    gap: 1rem;
}

.gap-3 {
    gap: 0.75rem;
}

/* Grid responsive classes */
.grid {
    display: grid;
}

.grid-cols-1 {
    grid-template-columns: repeat(1, minmax(0, 1fr));
}

.grid-cols-2 {
    grid-template-columns: repeat(2, minmax(0, 1fr));
}

@media (min-width: 768px) {
    .md\:grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (min-width: 1024px) {
    .lg\:grid-cols-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .lg\:col-span-2 {
        grid-column: span 2 / span 2;
    }

    .lg\:col-span-1 {
        grid-column: span 1 / span 1;
    }

    .lg\:block {
        display: block;
    }

    .lg\:hidden {
        display: none;
    }
}

@media (min-width: 1280px) {
    .xl\:grid-cols-4 {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    .xl\:col-span-1 {
        grid-column: span 1 / span 1;
    }

    .xl\:col-span-3 {
        grid-column: span 3 / span 3;
    }
}

/* Additional responsive utilities */
@media (max-width: 1023px) {
    .hidden.lg\:block {
        display: none !important;
    }
}

@media (min-width: 1024px) {
    .lg\:hidden {
        display: none !important;
    }
}

/* Positioning utilities */
.sticky {
    position: sticky;
}

.top-0 {
    top: 0;
}

.bottom-0 {
    bottom: 0;
}

/* Z-index utilities */
.z-50 {
    z-index: 50;
}

.z-40 {
    z-index: 40;
}

/* Width and height utilities */
.min-h-screen {
    min-height: 100vh;
}

.w-full {
    width: 100%;
}

.w-24 {
    width: 6rem;
}

.h-24 {
    height: 6rem;
}

.w-40 {
    width: 10rem;
}

.h-10 {
    height: 2.5rem;
}

.w-20 {
    width: 5rem;
}

.h-12 {
    height: 3rem;
}

.w-12 {
    width: 3rem;
}

/* Flexbox utilities */
.flex {
    display: flex;
}

.items-center {
    align-items: center;
}

.items-start {
    align-items: flex-start;
}

.justify-between {
    justify-content: space-between;
}

.justify-end {
    justify-content: flex-end;
}

.justify-center {
    justify-content: center;
}

.flex-1 {
    flex: 1 1 0%;
}

.flex-wrap {
    flex-wrap: wrap;
}

/* Text utilities */
.text-3xl {
    font-size: 1.875rem;
    line-height: 2.25rem;
}

.text-xl {
    font-size: 1.25rem;
    line-height: 1.75rem;
}

.text-lg {
    font-size: 1.125rem;
    line-height: 1.75rem;
}

.text-sm {
    font-size: 0.875rem;
    line-height: 1.25rem;
}

.text-xs {
    font-size: 0.75rem;
    line-height: 1rem;
}

.font-medium {
    font-weight: 500;
}

.font-bold {
    font-weight: 700;
}

.tracking-wide {
    letter-spacing: 0.025em;
}

.tracking-wider {
    letter-spacing: 0.05em;
}

.leading-relaxed {
    line-height: 1.625;
}

.text-center {
    text-align: center;
}

.text-left {
    text-align: left;
}

/* Color utilities */
.text-neutral-600 {
    color: #525252;
}

.text-neutral-500 {
    color: #737373;
}

.text-neutral-700 {
    color: #404040;
}

.text-neutral-400 {
    color: #a3a3a3;
}

.text-green-800 {
    color: #166534;
}

.text-red-800 {
    color: #991b1b;
}

.text-red-600 {
    color: #dc2626;
}

.text-red-800:hover {
    color: #7f1d1d;
}

.bg-white {
    background-color: var(--color-white);
}

.bg-neutral-50 {
    background-color: #fafafa;
}

.bg-neutral-100 {
    background-color: #f5f5f5;
}

.bg-green-50\/50 {
    background-color: rgba(240, 253, 244, 0.5);
}

.bg-red-50\/50 {
    background-color: rgba(254, 242, 242, 0.5);
}

.bg-white\/95 {
    background-color: rgba(255, 255, 255, 0.95);
}

.bg-neutral-50\/50 {
    background-color: rgba(250, 250, 250, 0.5);
}

/* Border utilities */
.border {
    border-width: 1px;
}

.border-b {
    border-bottom-width: 1px;
}

.border-t {
    border-top-width: 1px;
}

.border-l-4 {
    border-left-width: 4px;
}

.border-2 {
    border-width: 2px;
}

.border-dashed {
    border-style: dashed;
}

.border-neutral-200 {
    border-color: #e5e5e5;
}

.border-green-500 {
    border-color: #22c55e;
}

.border-red-500 {
    border-color: #ef4444;
}

.divide-y > :not([hidden]) ~ :not([hidden]) {
    border-top-width: 1px;
}

.rounded-xl {
    border-radius: 0.75rem;
}

.rounded-2xl {
    border-radius: 1rem;
}

.rounded-full {
    border-radius: 9999px;
}

.rounded-lg {
    border-radius: 0.5rem;
}

/* Spacing utilities */
.p-4 {
    padding: 1rem;
}

.p-6 {
    padding: 1.5rem;
}

.px-4 {
    padding-left: 1rem;
    padding-right: 1rem;
}

.px-6 {
    padding-left: 1.5rem;
    padding-right: 1.5rem;
}

.py-4 {
    padding-top: 1rem;
    padding-bottom: 1rem;
}

.py-5 {
    padding-top: 1.25rem;
    padding-bottom: 1.25rem;
}

.py-6 {
    padding-top: 1.5rem;
    padding-bottom: 1.5rem;
}

.py-8 {
    padding-top: 2rem;
    padding-bottom: 2rem;
}

.py-20 {
    padding-top: 5rem;
    padding-bottom: 5rem;
}

.mx-auto {
    margin-left: auto;
    margin-right: auto;
}

.mb-2 {
    margin-bottom: 0.5rem;
}

.mb-4 {
    margin-bottom: 1rem;
}

.mb-8 {
    margin-bottom: 2rem;
}

.mt-8 {
    margin-top: 2rem;
}

.-mx-6 {
    margin-left: -1.5rem;
    margin-right: -1.5rem;
}

/* Shadow utilities */
.shadow-sm {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.shadow-lg {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Backdrop utilities */
.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}

/* Overflow utilities */
.overflow-hidden {
    overflow: hidden;
}

.overflow-x-auto {
    overflow-x: auto;
}

/* Display utilities */
.hidden {
    display: none;
}

.block {
    display: block;
}

.inline {
    display: inline;
}

.inline-flex {
    display: inline-flex;
}

/* Maximum width utilities */
.max-w-7xl {
    max-width: 80rem;
}

.max-w-sm {
    max-width: 24rem;
}

/* Minimum width utilities */
.min-w-64 {
    min-width: 16rem;
}

/* Aspect ratio utilities */
.aspect-square {
    aspect-ratio: 1 / 1;
}

/* Transition utilities */
.transition-colors {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

.duration-150 {
    transition-duration: 150ms;
}

.duration-200 {
    transition-duration: 200ms;
}

/* Transform utilities */
.transform {
    transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

/* SVG utilities */
.fill-none {
    fill: none;
}

.stroke-current {
    stroke: currentColor;
}

/* List utilities */
.list-none {
    list-style-type: none;
}

.space-y-1 > :not([hidden]) ~ :not([hidden]) {
    margin-top: 0.25rem;
}

/* Table utilities */
.table {
    display: table;
}

.w-full {
    width: 100%;
}

/* Column span utilities */
.col-span-2 {
    grid-column: span 2 / span 2;
}

/* Additional responsive utilities for mobile */
@media (max-width: 1023px) {
    .lg\:hidden {
        display: none;
    }
}

@media (min-width: 1024px) {
    .hidden.lg\:block {
        display: block;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced form interactions
    const form = document.querySelector('form[action*="store"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            const button = form.querySelector('button[type="submit"]');
            const originalText = button.textContent;

            button.textContent = 'Saving...';
            button.disabled = true;

            // Reset after 2 seconds if form doesn't redirect
            setTimeout(() => {
                button.textContent = originalText;
                button.disabled = false;
            }, 2000);
        });
    }

    // Enhanced table row keyboard navigation
    const tableRows = document.querySelectorAll('.table-row');
    tableRows.forEach((row, index) => {
        row.setAttribute('tabindex', '0');

        row.addEventListener('keydown', (e) => {
            switch(e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    const nextRow = tableRows[index + 1];
                    if (nextRow) nextRow.focus();
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    const prevRow = tableRows[index - 1];
                    if (prevRow) prevRow.focus();
                    break;
                case 'Enter':
                case ' ':
                    e.preventDefault();
                    // Could trigger edit mode or show details
                    console.log('Row selected');
                    break;
            }
        });
    });

    // Focus first input helper
    window.focusFirstInput = function() {
        const firstInput = document.querySelector('#client');
        if (firstInput) {
            firstInput.focus();
            firstInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    };

    // Auto-resize textareas if any
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = this.scrollHeight + 'px';
        });
    });

    // Enhanced hover effects for mobile
    if ('ontouchstart' in window) {
        document.querySelectorAll('.table-row').forEach(row => {
            row.addEventListener('touchstart', function() {
                this.classList.add('hover-effect');
            });

            row.addEventListener('touchend', function() {
                setTimeout(() => {
                    this.classList.remove('hover-effect');
                }, 300);
            });
        });
    }

    // Smooth scroll to top when adding entry
    const addButtons = document.querySelectorAll('button[onclick*="focus"]');
    addButtons.forEach(button => {
        button.addEventListener('click', function() {
            setTimeout(() => {
                document.querySelector('#client').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 100);
        });
    });
});

// Alpine.js data for sticky filters
document.addEventListener('alpine:init', () => {
    Alpine.data('stickyFilters', () => ({
        isSticky: false,

        init() {
            const observer = new IntersectionObserver(
                ([entry]) => {
                    this.isSticky = entry.intersectionRatio < 1;
                },
                {
                    threshold: [1],
                    rootMargin: '-1px 0px 0px 0px'
                }
            );
            observer.observe(this.$el);
        }
    }));
});
</script>
@endsection
