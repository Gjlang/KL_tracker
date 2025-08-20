{{-- resources/views/partials/sidebar.blade.php
<!-- Mobile backdrop overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 md:hidden hidden"></div>

<!-- Sidebar -->
<aside id="app-sidebar"
       class="fixed inset-y-0 left-0 z-40 w-56 bg-gradient-to-br from-blue-600 via-blue-700 to-purple-700 transform -translate-x-full md:translate-x-0 transition-transform duration-200 ease-out shadow-xl">
  <div class="h-full flex flex-col">

    <!-- Logo/Brand -->
    <div class="flex items-center gap-3 p-6 border-b border-white/10">
      <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center shadow-lg">
        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
          <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
        </svg>
      </div>
      <span class="text-xl font-bold text-white">CodingLab</span>
    </div>

    <!-- Quick Search -->
    <div class="px-4 pt-4 pb-2">
      <div class="relative">
        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input type="text"
               placeholder="Quick Search ..."
               class="w-full bg-white/10 backdrop-blur-sm border border-white/20 rounded-xl py-3 pl-10 pr-4 text-white placeholder-white/60 text-sm focus:outline-none focus:ring-2 focus:ring-white/30 focus:border-white/40 transition-all">
      </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-4 py-2 overflow-y-auto">

      <!-- Main Menu Section -->
      <div class="mb-8">
        <div class="space-y-2">
          <!-- Dashboard -->
          <a href="{{ route('dashboard') }}"
             class="group flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-white/20 backdrop-blur-sm text-white shadow-lg' : 'text-white/80 hover:bg-white/10 hover:text-white hover:backdrop-blur-sm' }}">
            <div class="w-5 h-5 flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"/>
              </svg>
            </div>
            <span class="font-medium">Dashboard</span>
          </a>

          <!-- Projects (Calendar) -->
          <a href="{{ route('calendar.index') }}"
             class="group flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('calendar.*') ? 'bg-white/20 backdrop-blur-sm text-white shadow-lg' : 'text-white/80 hover:bg-white/10 hover:text-white hover:backdrop-blur-sm' }}">
            <div class="w-5 h-5 flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
              </svg>
            </div>
            <span class="font-medium">Projects</span>
          </a>

          <!-- Messages (KLTG Job) -->
          <a href="{{ route('dashboard.kltg') }}"
             class="group flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard.kltg') ? 'bg-white/20 backdrop-blur-sm text-white shadow-lg' : 'text-white/80 hover:bg-white/10 hover:text-white hover:backdrop-blur-sm' }}">
            <div class="w-5 h-5 flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
              </svg>
            </div>
            <span class="font-medium">Messages</span>
          </a>

          <!-- Analytics (Media Job) -->
          <a href="{{ route('dashboard.media') }}"
             class="group flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard.media') ? 'bg-white/20 backdrop-blur-sm text-white shadow-lg' : 'text-white/80 hover:bg-white/10 hover:text-white hover:backdrop-blur-sm' }}">
            <div class="w-5 h-5 flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
            </div>
            <span class="font-medium">Analytics</span>
          </a>

          <!-- Outdoor Job (Settings icon) -->
          <a href="{{ route('dashboard.outdoor') }}"
             class="group flex items-center gap-4 px-4 py-3 rounded-xl transition-all duration-200 {{ request()->routeIs('dashboard.outdoor') ? 'bg-white/20 backdrop-blur-sm text-white shadow-lg' : 'text-white/80 hover:bg-white/10 hover:text-white hover:backdrop-blur-sm' }}">
            <div class="w-5 h-5 flex items-center justify-center">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              </svg>
            </div>
            <span class="font-medium">Settings</span>
          </a>
        </div>
      </div>

    </nav>

    <!-- User Profile -->
    <div class="p-4 mt-auto border-t border-white/10">
      <div class="flex items-center gap-3 p-3 bg-white/10 backdrop-blur-sm rounded-xl">
        <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center shadow-md">
          <span class="text-sm font-bold text-white">{{ substr(auth()->user()->name ?? 'U', 0, 1) }}</span>
        </div>
        <div class="flex-1 min-w-0">
          <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name ?? 'User' }}</p>
          <p class="text-xs text-white/60 truncate">Admin</p>
        </div>
        <div class="w-6 h-6 bg-white/10 rounded-lg flex items-center justify-center cursor-pointer hover:bg-white/20 transition-colors">
          <svg class="w-4 h-4 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
          </svg>
        </div>
      </div>
    </div>

  </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('app-sidebar');
  const overlay = document.getElementById('sidebar-overlay');
  const openBtn = document.getElementById('open-sidebar');
  const main = document.querySelector('.app-main');

  function openSidebar() {
    sidebar.classList.remove('-translate-x-full');
    overlay.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
    if (main && window.innerWidth < 768) {
      main.classList.add('translate-x-56');
    }
  }

  function closeSidebar() {
    sidebar.classList.add('-translate-x-full');
    overlay.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    if (main) {
      main.classList.remove('translate-x-56');
    }
  }

  function resetSidebar() {
    if (window.innerWidth >= 768) {
      sidebar.classList.remove('-translate-x-full');
      overlay.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
      if (main) {
        main.classList.remove('translate-x-56');
      }
    } else {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
      if (main) {
        main.classList.remove('translate-x-56');
      }
    }
  }

  // Open sidebar button
  if (openBtn) {
    openBtn.addEventListener('click', (e) => {
      e.preventDefault();
      openSidebar();
    });
  }

  // Close on overlay click
  if (overlay) {
    overlay.addEventListener('click', closeSidebar);
  }

  // Close on Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeSidebar();
    }
  });

  // Reset on window resize
  window.addEventListener('resize', resetSidebar);

  // Initialize on load
  resetSidebar();
});
</script>

<style>
  /* Smooth scrolling and focus ring polish */
  html {
    scroll-behavior: smooth;
  }

  /* Custom focus rings for sidebar links */
  #app-sidebar a:focus {
    outline: 2px solid rgba(255, 255, 255, 0.3);
    outline-offset: 2px;
  }

  /* Hide scrollbar but keep functionality */
  #app-sidebar nav::-webkit-scrollbar {
    width: 4px;
  }

  #app-sidebar nav::-webkit-scrollbar-track {
    background: transparent;
  }

  #app-sidebar nav::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 2px;
  }

  #app-sidebar nav::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.3);
  }

  /* Custom backdrop blur support */
  .backdrop-blur-sm {
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
  }
</style> --}}
