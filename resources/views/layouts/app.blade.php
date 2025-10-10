<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Job Tracking System') }}</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Laravel Vite (CSS & JS) --}}
    {{-- Load Vite CSS first --}}
    @vite(['resources/css/app.css'])

    {{-- Per-page head --}}
    @yield('head')
    @stack('head')

    {{-- FullCalendar CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />

    {{-- Select2 CSS (loaded in head for styles, JS loaded in body) --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- DataTables Buttons CSS --}}
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    {{-- If using Bootstrap theme for buttons --}}
    {{-- <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css"> --}}

    <style>[x-cloak]{display:none!important}</style>
</head>

<body class="min-h-screen bg-[#F7F7F9] text-[#1C1E26] antialiased">
@php
  // Default container, pages can override via @section('container_class')
  $containerClass = trim(View::yieldContent('container_class')) ?: 'max-w-7xl mx-auto sm:px-6 lg:px-8';
@endphp

<div x-data="sidebar()" x-init="init()" class="min-h-screen flex">

  {{-- Mobile Overlay --}}
  <div x-show="isOpen" x-cloak
       @click="close()"
       class="fixed inset-0 z-40 bg-black bg-opacity-50 md:hidden"
       x-transition:enter="transition-opacity ease-linear duration-300"
       x-transition:enter-start="opacity-0"
       x-transition:enter-end="opacity-100"
       x-transition:leave="transition-opacity ease-linear duration-300"
       x-transition:leave-start="opacity-100"
       x-transition:leave-end="opacity-0">
  </div>

  {{-- LEFT: Sidebar (one owner only) --}}
  <aside :class="isOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
         class="fixed md:static inset-y-0 left-0 z-50 w-72 bg-white border-r border-neutral-200 transform transition-transform duration-300 ease-in-out md:transform-none overflow-y-auto">
    @includeIf('partials.sidebar')
  </aside>

  {{-- RIGHT: Content column --}}
  <div class="flex-1 flex flex-col min-h-screen min-w-0">

    <main class="py-6 flex-1 min-h-0">
      <div class="{{ $containerClass }}">
        {{-- Flash messages --}}
        @if (session('success'))
          <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
          </div>
        @endif
        @if (session('error'))
          <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
          </div>
        @endif

        @yield('content')
      </div>
    </main>
    @yield('modal_content')

    @stack('modals')

  </div>
</div>

{{-- Load Vite JS (if you have app-specific JS that might use jQuery) --}}
{{-- Make sure Vite's app.js doesn't redefine $ or cause conflicts if jQuery is also loaded via CDN --}}
@vite(['resources/js/app.js'])

{{-- Load external libraries in the correct order --}}

{{-- 1. jQuery (Required by DataTables and Select2) --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

{{-- 2. DataTables JS (Depends on jQuery) --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script> <!-- If using Bootstrap theme -->

{{-- 3. DataTables Buttons Extension JS (Depends on DataTables core) --}}
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>

{{-- 3.1 JSZip MUST be before buttons.html5 --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

{{-- 3.2 PDF dependencies (optional, must be BEFORE buttons.html5) --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

{{-- 3.3 Buttons HTML5/Print AFTER dependencies --}}
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script> <!-- add this -->

{{-- Optional: Bootstrap integration for Buttons --}}
{{-- <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script> --}}

{{-- 4. Select2 JS (Depends on jQuery) --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

{{-- FullCalendar JS --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

{{-- Alpine.js (Loaded last among external libs, 'defer' is fine) --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

{{-- Alpine sidebar controller and global helper functions --}}
<script>
  function sidebar() {
    return {
      isOpen: false,
      init() {
        this.isOpen = window.matchMedia('(min-width: 768px)').matches;
        window.addEventListener('resize', () => {
          this.isOpen = window.matchMedia('(min-width: 768px)').matches;
        });
      },
      open() { this.isOpen = true; },
      close() { this.isOpen = false; },
      toggle() { this.isOpen = !this.isOpen; }
    }
  }

  // Legacy modal helpers
  window.openModal = function(id){
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.remove('hidden');
    el.classList.add('show');
    el.style.display = 'flex';     // match .modal.show { display:flex }
    document.body.classList.add('overflow-hidden');
  }

  window.closeModal = function(id){
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add('hidden');
    el.classList.remove('show');
    el.style.display = 'none';
    document.body.classList.remove('overflow-hidden');
  }

  // Global Toast Notification Function
  window.showSubmitToast = function(message, color) {
    // Create container if missing
    let container = document.getElementById('toast-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'toast-container';
      container.className = 'fixed top-4 right-4 z-[10000] space-y-2';
      container.style.cssText = 'pointer-events: none;';
      document.body.appendChild(container);
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'rounded-xl shadow-lg px-6 py-4 text-sm font-medium text-white transition-all duration-300 transform translate-x-0 opacity-100';
    toast.style.cssText = `background-color: ${color || '#91C714'}; pointer-events: auto; min-width: 250px;`;
    toast.textContent = message || 'Action completed';

    // Add to container
    container.appendChild(toast);

    // Animate in
    setTimeout(() => {
      toast.style.transform = 'translateX(0)';
      toast.style.opacity = '1';
    }, 10);

    // Remove after delay
    setTimeout(() => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateX(20px)';
      setTimeout(() => {
        toast.remove();
        // Remove container if empty
        if (container.children.length === 0) {
          container.remove();
        }
      }, 300);
    }, 3000);
  };

  // Allow [data-toggle="modal"] to work (Bootstrap-like)
  document.addEventListener('click', (e) => {
    const t = e.target.closest('[data-toggle="modal"]');
    if (!t) return;
    e.preventDefault();
    const targetSel = t.getAttribute('data-target');
    if (!targetSel) return;
    const id = targetSel.startsWith('#') ? targetSel.slice(1) : targetSel;
    openModal(id);
  });

  // Allow [data-dismiss="modal"] to work
  document.addEventListener('click', (e) => {
    const dismissBtn = e.target.closest('[data-dismiss="modal"]');
    if (!dismissBtn) return;
    e.preventDefault();
    const modal = dismissBtn.closest('.modal');
    if (modal) {
      closeModal(modal.id);
    }
  });
</script>

{{-- Page scripts (Yielded content like your billboard script will go here) --}}
{{-- This must come AFTER all dependencies (jQuery, DataTables, Select2) --}}
@yield('scripts')
@stack('scripts')
</body>
</html>
