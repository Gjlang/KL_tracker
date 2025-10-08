<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Job Tracking System')); ?></title>

    
    <link rel="preconnect" href="https://fonts.bunny.net  ">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    
    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>

    
    <?php echo $__env->yieldContent('head'); ?>
    <?php echo $__env->yieldPushContent('head'); ?>

    
    <link href="  https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css  " rel="stylesheet" />

    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css  " rel="stylesheet" />

    
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    
    

    <style>[x-cloak]{display:none!important}</style>
</head>

<body class="min-h-screen bg-[#F7F7F9] text-[#1C1E26] antialiased">
<?php
  // Default container, pages can override via @section('container_class')
  $containerClass = trim(View::yieldContent('container_class')) ?: 'max-w-7xl mx-auto sm:px-6 lg:px-8';
?>

<div x-data="sidebar()" x-init="init()" class="min-h-screen flex">

  
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

  
  <aside :class="isOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
         class="fixed md:static inset-y-0 left-0 z-50 w-72 bg-white border-r border-neutral-200 transform transition-transform duration-300 ease-in-out md:transform-none overflow-y-auto">
    <?php if ($__env->exists('partials.sidebar')) echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  </aside>

  
  <div class="flex-1 flex flex-col min-h-screen min-w-0">

    <main class="py-6 flex-1 min-h-0">
      <div class="<?php echo e($containerClass); ?>">
        
        <?php if(session('success')): ?>
          <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            <?php echo e(session('success')); ?>

          </div>
        <?php endif; ?>
        <?php if(session('error')): ?>
          <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <?php echo e(session('error')); ?>

          </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
      </div>
    </main>

    <?php echo $__env->yieldPushContent('modals'); ?>

  </div>
</div>



<?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.js']); ?>




<script src="https://code.jquery.com/jquery-3.7.1.min.js  " integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>


<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js  "></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js  "></script> <!-- If using Bootstrap theme -->


<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" integrity="sha512-Xf/tPwC9z3TvP1x/8j2pLgltKZI2HvZPMqkYz3a3WfV4RfF+8Jg2JLqjZ+9yT578Z8ZJ9Lc+YUI0Bp5f8b7OvQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" integrity="sha512-5y5Vw1f5z7Hk20wZg9qyEo+7+Hx4lgxYJLnWJkQo4UAb36BXEGrnU+aS7LfMnJFyL0pUqV0mL6pGF4Ud3Kg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" integrity="sha512-5y5Vw1f5z7Hk20wZg9qyEo+7+Hx4lgxYJLnWJkQo4UAb36BXEGrnU+aS7LfMnJFyL0pUqV0mL6pGF4Ud3Kg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>




<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js  "></script>


<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js  "></script>


<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js  "></script>


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



<?php echo $__env->yieldContent('scripts'); ?>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html><?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views\layouts\app.blade.php ENDPATH**/ ?>