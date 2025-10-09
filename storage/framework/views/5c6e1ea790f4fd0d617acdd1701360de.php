<aside
  class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-neutral-200
         transform transition-transform duration-200
         -translate-x-full md:flex md:flex-col"
  :class="isOpen ? 'translate-x-0' : '-translate-x-full'"
  x-cloak
>
  <div class="h-14 flex items-center gap-3 px-4 border-b border-neutral-200">
    
    <div class="w-9 h-9 rounded-xl flex items-center justify-center cursor-pointer relative z-50"
         style="background:#22255b;"
         title="Close sidebar"
         @click.stop="close()">
      <svg class="w-5 h-5 text-white pointer-events-none" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
      </svg>
    </div>
    <span class="font-semibold cursor-pointer select-none" @click.stop="close()">Navigation</span>
  </div>

  <nav class="flex-1 overflow-y-auto py-3" x-data="sidebarState()">

    
    <div class="px-3 mb-2">
      <a href="<?php echo e(route('dashboard')); ?>"
         @click="if (window.matchMedia('(max-width: 767px)').matches) close()"
         class="block px-3 py-2 rounded-lg font-medium text-sm
                <?php echo e(request()->routeIs('dashboard') ? 'bg-[#22255b] text-white' : 'hover:bg-neutral-50'); ?>">
        <i class="fas fa-home mr-2"></i> Dashboard
      </a>
    </div>

    
    <div class="px-3">
      <button @click="toggle('kltg')" class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-neutral-50">
        <span class="font-medium">KLTG</span>
        <svg class="w-4 h-4 transition-transform duration-200" :class="open.kltg ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
      <div x-show="open.kltg"
           x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="opacity-0 transform scale-95"
           x-transition:enter-end="opacity-100 transform scale-100"
           x-transition:leave="transition ease-in duration-150"
           x-transition:leave-start="opacity-100 transform scale-100"
           x-transition:leave-end="opacity-0 transform scale-95">
        <a href="<?php echo e(route('dashboard.kltg')); ?>"
           @click="if (window.matchMedia('(max-width: 767px)').matches) close()"
           class="block mt-1 mx-3 px-3 py-2 rounded-lg text-sm <?php echo e(request()->routeIs('dashboard.kltg') ? 'bg-[#22255b] text-white' : 'hover:bg-neutral-50'); ?>">
          Monthly
        </a>
        <?php if(Route::has('coordinator.kltg.index')): ?>
        <a href="<?php echo e(route('coordinator.kltg.index')); ?>"
           @click="if (window.matchMedia('(max-width: 767px)').matches) close()"
           class="block mt-1 mx-3 px-3 py-2 rounded-lg text-sm <?php echo e(request()->routeIs('coordinator.kltg.*') ? 'bg-[#22255b] text-white' : 'hover:bg-neutral-50'); ?>">
          Coordinator List
        </a>
        <?php endif; ?>
      </div>
    </div>

    
    <div class="px-3 mt-2">
      <button @click="toggle('media')" class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-neutral-50">
        <span class="font-medium">Social Media</span>
        <svg class="w-4 h-4 transition-transform duration-200" :class="open.media ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
      <div x-show="open.media"
           x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="opacity-0 transform scale-95"
           x-transition:enter-end="opacity-100 transform scale-100"
           x-transition:leave="transition ease-in duration-150"
           x-transition:leave-start="opacity-100 transform scale-100"
           x-transition:leave-end="opacity-0 transform scale-95">
        <a href="<?php echo e(route('dashboard.media')); ?>"
           @click="if (window.matchMedia('(max-width: 767px)').matches) close()"
           class="block mt-1 mx-3 px-3 py-2 rounded-lg text-sm <?php echo e(request()->routeIs('dashboard.media') ? 'bg-[#22255b] text-white' : 'hover:bg-neutral-50'); ?>">
          Monthly
        </a>
        <?php if(Route::has('coordinator.media.index')): ?>
        <a href="<?php echo e(route('coordinator.media.index')); ?>"
           @click="if (window.matchMedia('(max-width: 767px)').matches) close()"
           class="block mt-1 mx-3 px-3 py-2 rounded-lg text-sm <?php echo e(request()->routeIs('coordinator.media.*') ? 'bg-[#22255b] text-white' : 'hover:bg-neutral-50'); ?>">
          Coordinator List
        </a>
        <?php endif; ?>
      </div>
    </div>

    
    <div class="px-3 mt-2">
      <button @click="toggle('outdoor')" class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-neutral-50">
        <span class="font-medium">Outdoor</span>
        <svg class="w-4 h-4 transition-transform duration-200" :class="open.outdoor ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
      <div x-show="open.outdoor"
           x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="opacity-0 transform scale-95"
           x-transition:enter-end="opacity-100 transform scale-100"
           x-transition:leave="transition ease-in duration-150"
           x-transition:leave-start="opacity-100 transform scale-100"
           x-transition:leave-end="opacity-0 transform scale-95">
        <a href="<?php echo e(route('billboard.index')); ?>"
           @click="if (window.matchMedia('(max-width: 767px)').matches) close()"
           class="block mt-1 mx-3 px-3 py-2 rounded-lg text-sm <?php echo e(request()->routeIs('billboard.index') ? 'bg-[#22255b] text-white' : 'hover:bg-neutral-50'); ?>">
          Billboard Stock inventory
        </a>
        <a href="<?php echo e(route('billboard.availability.index')); ?>"
           @click="if (window.matchMedia('(max-width: 767px)').matches) close()"
           class="block mt-1 mx-3 px-3 py-2 rounded-lg text-sm <?php echo e(request()->routeIs('billboard.availability.index') ? 'bg-[#22255b] text-white' : 'hover:bg-neutral-50'); ?>">
          Billboard Availability
        </a>
        <a href="<?php echo e(route('dashboard.outdoor')); ?>"
           @click="if (window.matchMedia('(max-width: 767px)').matches) close()"
           class="block mt-1 mx-3 px-3 py-2 rounded-lg text-sm <?php echo e(request()->routeIs('dashboard.outdoor') ? 'bg-[#22255b] text-white' : 'hover:bg-neutral-50'); ?>">
          Monthly
        </a>
        <?php if(Route::has('coordinator.outdoor.index')): ?>
        <a href="<?php echo e(route('coordinator.outdoor.index')); ?>"
           @click="if (window.matchMedia('(max-width: 767px)').matches) close()"
           class="block mt-1 mx-3 px-3 py-2 rounded-lg text-sm <?php echo e(request()->routeIs('coordinator.outdoor.*') ? 'bg-[#22255b] text-white' : 'hover:bg-neutral-50'); ?>">
          Coordinator List
        </a>
        <?php endif; ?>
        <?php if(Route::has('outdoor.whiteboard.index')): ?>
        <a href="<?php echo e(route('outdoor.whiteboard.index')); ?>"
           @click="if (window.matchMedia('(max-width: 767px)').matches) close()"
           class="block mt-1 mx-3 px-3 py-2 rounded-lg text-sm <?php echo e(request()->routeIs('outdoor.whiteboard.*') ? 'bg-[#22255b] text-white' : 'hover:bg-neutral-50'); ?>">
          Outdoor Whiteboard
        </a>
        <?php endif; ?>
        <a href="<?php echo e(route('dashboard.outdoor')); ?>"
           @click="if (window.matchMedia('(max-width: 767px)').matches) close()"
           class="block mt-1 mx-3 px-3 py-2 rounded-lg text-sm <?php echo e(request()->routeIs('dashboard.outdoor') ? 'bg-[#22255b] text-white' : 'hover:bg-neutral-50'); ?>">
          Vendor Stock Inventory
        </a>
      </div>
    </div>

    
    <div class="px-3 mt-2">
      <button @click="toggle('outdoor')" class="w-full flex items-center justify-between px-3 py-2 rounded-lg hover:bg-neutral-50">
        <span class="font-medium">Management</span>
        <svg class="w-4 h-4 transition-transform duration-200" :class="open.outdoor ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor">
          <path stroke-linecap="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
      </button>
      <div x-show="open.outdoor"
           x-transition:enter="transition ease-out duration-200"
           x-transition:enter-start="opacity-0 transform scale-95"
           x-transition:enter-end="opacity-100 transform scale-100"
           x-transition:leave="transition ease-in duration-150"
           x-transition:leave-start="opacity-100 transform scale-100"
           x-transition:leave-end="opacity-0 transform scale-95">
        <a href="<?php echo e(route('dashboard.outdoor')); ?>"
           @click="if (window.matchMedia('(max-width: 767px)').matches) close()"
           class="block mt-1 mx-3 px-3 py-2 rounded-lg text-sm <?php echo e(request()->routeIs('dashboard.outdoor') ? 'bg-[#22255b] text-white' : 'hover:bg-neutral-50'); ?>">
          Users
        </a>
        <a href="<?php echo e(route('client-company.index')); ?>"
           @click="if (window.matchMedia('(max-width: 767px)').matches) close()"
           class="block mt-1 mx-3 px-3 py-2 rounded-lg text-sm <?php echo e(request()->routeIs('client-company.index') ? 'bg-[#22255b] text-white' : 'hover:bg-neutral-50'); ?>">
          Clients
        </a>
        <a href="<?php echo e(route('contractors.index')); ?>"
            @click="if (window.matchMedia('(max-width: 767px)').matches) close()"
            class="block mt-1 mx-3 px-3 py-2 rounded-lg text-sm <?php echo e(request()->routeIs('contractors.*') ? 'bg-[#22255b] text-white' : 'hover:bg-neutral-50'); ?>">
            Contractors
        </a>
      </div>
    </div>

  </nav>
</aside>


<div class="fixed inset-0 bg-black/40 z-40 md:hidden"
     x-show="isOpen" x-transition.opacity @click="close()" x-cloak></div>

<script>
  function sidebarState() {
    const key = 'sidebar-open-groups';
    let saved = {};
    try { saved = JSON.parse(localStorage.getItem(key)) ?? {}; } catch(e) {}
    return {
      open: Object.assign({ kltg: true, media: true, outdoor: true }, saved),
      toggle(section) {
        this.open[section] = !this.open[section];
        localStorage.setItem(key, JSON.stringify(this.open));
      },
    }
  }
</script>
<?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\partials\sidebar.blade.php ENDPATH**/ ?>