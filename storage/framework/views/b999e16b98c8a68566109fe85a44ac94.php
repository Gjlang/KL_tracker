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

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50">
<?php
  // Kelas container bisa di-override dari halaman:
  // @section('container_class', 'w-screen max-w-none px-0')
  $containerClass = trim(View::yieldContent('container_class')) ?: 'max-w-7xl mx-auto sm:px-6 lg:px-8';
?>

<div class="min-h-screen">
  
  <nav class="glassmorphism shadow-soft border-b border-white/20 sticky top-0 z-50">
    <div class="<?php echo e($containerClass); ?>">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <div class="shrink-0 flex items-center gap-3">
            <div class="w-10 h-10 gradient-bg rounded-xl flex items-center justify-center shadow-lg">
              <i class="fas fa-briefcase text-white text-lg"></i>
            </div>
            <a href="<?php echo e(route('dashboard')); ?>" class="text-xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent">
              MASTER CLIENTELE
            </a>
          </div>

          <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
            <a href="<?php echo e(route('dashboard')); ?>"
               class="nav-link inline-flex items-center px-1 pt-1 border-b-2 <?php echo e(request()->routeIs('dashboard') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700'); ?> text-sm font-medium">
              <i class="fas fa-tachometer-alt mr-2 text-sm"></i> Dashboard
            </a>
          </div>
        </div>
      </div>
    </div>
  </nav>

  
  <main class="py-6">
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
</div>

<?php echo $__env->yieldPushContent('modals'); ?>


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