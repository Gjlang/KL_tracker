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
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Per-page head --}}
    @yield('head')
    @stack('head')

    {{-- FullCalendar CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />

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

    @stack('modals')

  </div>
</div>

{{-- FullCalendar JS --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

{{-- Alpine.js --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

{{-- Alpine sidebar controller --}}
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
  function openModal(id){
    document.getElementById(id)?.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
  }
  function closeModal(id){
    document.getElementById(id)?.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
  }
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
      document.querySelectorAll('.modal').forEach(m => m.classList.add('hidden'));
      document.body.classList.remove('overflow-hidden');
    }
  });
</script>

{{-- Page scripts --}}
@yield('scripts')
@stack('scripts')
</body>
</html>
