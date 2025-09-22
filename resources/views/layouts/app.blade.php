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

<body class="font-sans antialiased bg-gray-50">
@php
  // Allow pages to override container width with:
  // @section('container_class', 'w-screen max-w-none px-0')
  $containerClass = trim(View::yieldContent('container_class')) ?: 'max-w-7xl mx-auto sm:px-6 lg:px-8';
@endphp

<div class="min-h-screen">

  {{-- ====== ALWAYS-OPEN LAYOUT: 2 columns (18rem sidebar + content) ====== --}}
  <div class="min-h-screen grid grid-cols-[18rem_1fr]">

    {{-- LEFT: Sidebar (always visible) --}}
    <aside class="bg-white border-r border-neutral-200 sticky top-0 h-screen overflow-y-auto">
      {{-- Use your existing sidebar partial/component here --}}
      @includeIf('partials.sidebar')
      {{-- If you don’t have partials/sidebar.blade.php yet, create it
           and put your nav links inside. --}}
    </aside>

    {{-- RIGHT: Content column --}}
    <div class="flex flex-col min-h-screen">

      {{-- Header (kept simple; removed “MASTER CLIENTELE”) --}}
      <header class="sticky top-0 z-10 bg-white border-b border-neutral-200">
        <div class="h-16 px-4 flex items-center justify-between">
          <div class="flex items-center gap-3">
            {{-- Place a small logo if you want; no toggle anymore --}}
            {{-- <img src="{{ asset('images/logo.svg') }}" class="h-7 w-auto" alt="Logo"> --}}
            <span class="text-sm text-neutral-500">Sidebar pinned</span>
          </div>

          <div class="flex items-center gap-2">
            @auth
              <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="px-3 py-2 rounded-full bg-[#22255b] text-white hover:bg-[#1a1e4a] focus:ring-2 focus:ring-[#4bbbed]">
                  Logout
                </button>
              </form>
            @endauth
          </div>
        </div>
      </header>

      {{-- PAGE CONTENT --}}
      <main class="py-6 flex-1">
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
</div>

{{-- (Optional) FullCalendar JS global --}}
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

{{-- Global helpers --}}
<script>
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
