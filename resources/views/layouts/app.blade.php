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

    {{-- FullCalendar CSS (optional global) --}}
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />

    <style>[x-cloak]{display:none!important}</style>
</head>

<body class="min-h-screen bg-[#F7F7F9] text-[#1C1E26] antialiased">
@php
  // Allow pages to override container width with:
  // @section('container_class', 'w-screen max-w-none px-0')
  $containerClass = trim(View::yieldContent('container_class')) ?: 'max-w-7xl mx-auto sm:px-6 lg:px-8';
@endphp

{{-- Root Alpine untuk seluruh shell --}}
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

  {{-- LEFT: Sidebar --}}
  <aside :class="isOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
         class="fixed md:static inset-y-0 left-0 z-50 w-72 bg-white border-r border-neutral-200 transform transition-transform duration-300 ease-in-out md:transform-none overflow-y-auto">
    {{-- Use your existing sidebar partial/component here --}}
    @includeIf('partials.sidebar')
    {{-- If you don't have partials/sidebar.blade.php yet, create it
         and put your nav links inside. --}}
  </aside>

  {{-- RIGHT: Content column --}}
  <div class="flex-1 flex flex-col min-h-screen min-w-0">

    {{-- Header --}}
    <header class="sticky top-0 z-30 bg-white border-b border-neutral-200">
      <div class="h-14 px-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
          {{-- Tombol toggle untuk mobile --}}
          <button class="md:hidden p-2 rounded border border-neutral-300 hover:bg-neutral-50"
                  @click="toggle()"
                  aria-label="Toggle sidebar">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
          </button>

          {{-- App Title/Logo --}}
          <a href="{{ route('dashboard') }}" class="font-medium text-lg">
            KL Guide Tracker
          </a>
        </div>

        <div class="flex items-center gap-2">
          @auth
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="px-3 py-2 rounded-full bg-[#22255b] text-white hover:bg-[#1a1e4a] focus:ring-2 focus:ring-[#4bbbed] transition-colors">
                Logout
              </button>
            </form>
          @endauth
        </div>
      </div>
    </header>

    {{-- PAGE CONTENT --}}
    <main class="py-6 flex-1 min-h-0">
      <div class="{{ $containerClass }}">
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

{{-- (Optional) FullCalendar JS global --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

{{-- Alpine.js CDN (if not included in Vite bundle) --}}
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

{{-- Global helpers --}}
<script>
  // Alpine.js Controller untuk buka/tutup sidebar
  function sidebar() {
    return {
      isOpen: false,
      init() {
        // Pinned di desktop (>= md breakpoint)
        this.isOpen = window.matchMedia('(min-width: 768px)').matches;

        // Sinkron saat resize window
        window.addEventListener('resize', () => {
          this.isOpen = window.matchMedia('(min-width: 768px)').matches;
        });
      },
      open() {
        this.isOpen = true;
      },
      close() {
        this.isOpen = false;
      },
      toggle() {
        this.isOpen = !this.isOpen;
      }
    }
  }

  // Legacy modal functions (keep for backward compatibility)
  function openModal(id){document.getElementById(id)?.classList.remove('hidden');document.body.classList.add('overflow-hidden');}
  function closeModal(id){document.getElementById(id)?.classList.add('hidden');document.body.classList.remove('overflow-hidden');}
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
