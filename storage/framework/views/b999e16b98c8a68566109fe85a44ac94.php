<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'Job Tracking System')); ?></title>

    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>

    
    <?php echo $__env->yieldContent('head'); ?>
    <?php echo $__env->yieldPushContent('head'); ?>

    
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet" />

    <style>[x-cloak]{display:none!important}</style>
</head>

<body class="font-sans antialiased bg-gray-50">
<?php
  // Allow pages to override container width with:
  // @section('container_class', 'w-screen max-w-none px-0')
  $containerClass = trim(View::yieldContent('container_class')) ?: 'max-w-7xl mx-auto sm:px-6 lg:px-8';
?>

<div class="min-h-screen">

  
  <div class="min-h-screen grid grid-cols-[18rem_1fr]">

    
    <aside class="bg-white border-r border-neutral-200 sticky top-0 h-screen overflow-y-auto">
      
      <?php if ($__env->exists('partials.sidebar')) echo $__env->make('partials.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
      
    </aside>

    
    <div class="flex flex-col min-h-screen">

      
      <header class="sticky top-0 z-10 bg-white border-b border-neutral-200">
        <div class="h-16 px-4 flex items-center justify-between">
          <div class="flex items-center gap-3">
            
            
            <span class="text-sm text-neutral-500">Sidebar pinned</span>
          </div>

          <div class="flex items-center gap-2">
            <?php if(auth()->guard()->check()): ?>
              <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button class="px-3 py-2 rounded-full bg-[#22255b] text-white hover:bg-[#1a1e4a] focus:ring-2 focus:ring-[#4bbbed]">
                  Logout
                </button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </header>

      
      <main class="py-6 flex-1">
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
</div>


<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>


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


<?php echo $__env->yieldContent('scripts'); ?>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views/layouts/app.blade.php ENDPATH**/ ?>