{{-- App Shell with persistent sidebar --}}
@props(['title' => config('app.name', 'Job Tracker')])

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>{{ $title }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite(['resources/css/app.css','resources/js/app.js'])

  {{-- Alpine.js with Collapse plugin --}}
  <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>[x-cloak]{display:none !important}</style>
  @stack('head')
</head>
<body class="bg-[#F7F7F9] text-[#1C1E26] antialiased">

  <div x-data="appShell()" x-init="init()" class="min-h-screen flex">
    @include('partials.sidebar')

    {{-- MAIN COLUMN: expands when sidebar is closed --}}
    <div class="flex-1 min-w-0 transition-[padding] duration-200"
         :class="isOpen ? 'md:pl-72' : ''">

      <header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-neutral-200">
        <div class="px-4 sm:px-6 lg:px-8 h-14 flex items-center justify-between">
          <div class="flex items-center gap-3">
            {{-- Single toggle button (simplified) --}}
            <button @click="toggleSidebar"
                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-neutral-200 hover:bg-neutral-50 transition"
                    aria-label="Toggle sidebar">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
              </svg>
            </button>

            <a href="{{ route('dashboard') }}" class="font-semibold tracking-tight select-none">
              {{ config('app.name', 'Job Tracker') }}
            </a>
          </div>
        </div>
      </header>

      <main class="p-4 sm:p-6 lg:p-8">
        {{ $slot }}
      </main>
    </div>
  </div>

  @stack('scripts')

  <script>
    function appShell() {
      const KEY = 'sidebar:open';
      const mq  = window.matchMedia('(min-width: 768px)');
      let saved = localStorage.getItem(KEY);
      let _isOpen = saved !== null ? (saved === '1') : mq.matches;

      function set(v){
        _isOpen = !!v;
        localStorage.setItem(KEY, _isOpen ? '1' : '0');
      }

      return {
        get isOpen(){ return _isOpen },
        set isOpen(v){ set(v) },
        open(){ set(true) },
        close(){ set(false) },
        toggleSidebar(){ set(!_isOpen) },
        init(){
          // follow breakpoint only until user makes a choice
          mq.addEventListener?.('change', e => {
            if (localStorage.getItem(KEY) === null) set(e.matches);
          });
        }
      }
    }
  </script>
</body>
</html>
