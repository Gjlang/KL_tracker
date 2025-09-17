<?php
  // Brand tokens
  $brand = '#22255b';   // primary
  $focus = '#4bbbed';   // focus ring
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
  <title>Login • <?php echo e(config('app.name', 'App')); ?></title>
  <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
  <style>
    :root {
      --brand: <?php echo e($brand); ?>;
      --focus: <?php echo e($focus); ?>;
      --brand-light: color-mix(in srgb, <?php echo e($brand); ?> 10%, white);
      --brand-dark: color-mix(in srgb, <?php echo e($brand); ?> 15%, black);
    }

    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
    }

    .glass-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.2);
      box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
      color: #fff;
      transition: all .3s ease;
      box-shadow: 0 4px 15px 0 rgba(34, 37, 91, 0.3);
      border: none;
      position: relative;
      overflow: hidden;
    }

    .btn-primary::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s;
    }

    .btn-primary:hover::before {
      left: 100%;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px 0 rgba(34, 37, 91, 0.4);
    }

    .btn-primary:active {
      transform: translateY(0);
    }

    .btn-primary:disabled {
      opacity: .7;
      cursor: not-allowed;
      transform: none;
      box-shadow: 0 4px 15px 0 rgba(34, 37, 91, 0.2);
    }

    .input {
      width: 100%;
      border: 2px solid #e5e7eb;
      background: rgba(255, 255, 255, 0.9);
      border-radius: .75rem;
      padding: 1rem 1.25rem;
      font-size: 1rem;
      line-height: 1.5rem;
      transition: all .3s ease;
      backdrop-filter: blur(10px);
    }

    .input:focus {
      outline: none;
      border-color: var(--focus);
      background: rgba(255, 255, 255, 1);
      box-shadow: 0 0 0 4px color-mix(in srgb, var(--focus) 15%, transparent);
      transform: translateY(-1px);
    }

    .input.is-invalid {
      border-color: #ef4444;
      box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
    }

    .help-error {
      color: #dc2626;
      font-size: .875rem;
      margin-top: .5rem;
      font-weight: 500;
    }

    .form-group {
      position: relative;
    }

    .form-label {
      display: block;
      font-size: .875rem;
      font-weight: 600;
      color: #374151;
      margin-bottom: .5rem;
      transition: color .2s ease;
    }

    .input:focus + .form-label,
    .form-group:focus-within .form-label {
      color: var(--focus);
    }

    .toggle-password {
      position: absolute;
      right: 1rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #6b7280;
      font-size: .875rem;
      font-weight: 500;
      padding: .5rem;
      border-radius: .375rem;
      transition: all .2s ease;
      cursor: pointer;
    }

    .toggle-password:hover {
      color: var(--brand);
      background: rgba(34, 37, 91, 0.05);
    }

    .checkbox-custom {
      appearance: none;
      width: 1rem;
      height: 1rem;
      border: 2px solid #d1d5db;
      border-radius: .25rem;
      background: white;
      position: relative;
      cursor: pointer;
      transition: all .2s ease;
    }

    .checkbox-custom:checked {
      background: var(--brand);
      border-color: var(--brand);
    }

    .checkbox-custom:checked::after {
      content: '✓';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      color: white;
      font-size: .75rem;
      font-weight: bold;
    }

    .checkbox-custom:hover {
      border-color: var(--focus);
    }

    .error-alert {
      background: linear-gradient(135deg, #fef2f2 0%, #fde8e8 100%);
      border: 1px solid #fecaca;
      color: #dc2626;
      padding: 1rem;
      border-radius: .75rem;
      font-size: .875rem;
      margin-bottom: 1.5rem;
      font-weight: 500;
      box-shadow: 0 4px 6px -1px rgba(220, 38, 38, 0.1);
    }

    .link-primary {
      color: var(--focus);
      text-decoration: none;
      font-weight: 500;
      transition: all .2s ease;
      position: relative;
    }

    .link-primary::after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      bottom: -2px;
      left: 0;
      background: var(--focus);
      transition: width .3s ease;
    }

    .link-primary:hover::after {
      width: 100%;
    }

    .link-primary:hover {
      color: var(--brand);
    }

    .title {
      background: linear-gradient(135deg, var(--brand) 0%, var(--focus) 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      font-weight: 700;
      font-size: 1.875rem;
      margin-bottom: 2rem;
      text-align: center;
    }

    /* Keep autofill clean */
    input:-webkit-autofill {
      -webkit-box-shadow: 0 0 0 1000px rgba(255, 255, 255, 0.9) inset;
      -webkit-text-fill-color: #111827;
    }

    /* Subtle animations */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .fade-in-up {
      animation: fadeInUp 0.6s ease forwards;
    }

    .stagger-1 { animation-delay: 0.1s; opacity: 0; }
    .stagger-2 { animation-delay: 0.2s; opacity: 0; }
    .stagger-3 { animation-delay: 0.3s; opacity: 0; }
    .stagger-4 { animation-delay: 0.4s; opacity: 0; }

    /* Tab styling */
    .tab-button {
      padding: 0.75rem 1.5rem;
      border-radius: 0.75rem;
      border: 2px solid #e5e7eb;
      background: rgba(255, 255, 255, 0.5);
      color: #6b7280;
      font-weight: 500;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .tab-button.active {
      background: var(--brand);
      border-color: var(--brand);
      color: white;
      box-shadow: 0 4px 15px 0 rgba(34, 37, 91, 0.3);
    }

    .tab-button:hover:not(.active) {
      border-color: var(--focus);
      background: rgba(255, 255, 255, 0.8);
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
  <main class="w-full max-w-md glass-card rounded-3xl p-10 fade-in-up">

    <!-- Tab Switcher -->
    <div class="flex gap-2 mb-6 stagger-1 fade-in-up">
      <button type="button" id="loginTab" class="tab-button active flex-1 text-center">Sign in</button>
      <button type="button" id="registerTab" class="tab-button flex-1 text-center">Create account</button>
    </div>

    <!-- Login Section -->
    <div id="loginSection">
      <h1 class="title stagger-1 fade-in-up">Sign in</h1>

      <?php if($errors->any()): ?>
        <div class="error-alert stagger-2 fade-in-up" role="alert">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div><?php echo e($error); ?></div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="<?php echo e(route('login.store')); ?>" id="login-form" class="space-y-6" novalidate>
        <?php echo csrf_field(); ?>

        
        <div class="form-group stagger-3 fade-in-up">
          <label for="login" class="form-label">Email or username</label>
          <input
            id="login"
            name="login"
            type="text"
            required
            autocomplete="username"
            autofocus
            value="<?php echo e(old('login')); ?>"
            class="input <?php echo e($errors->has('login') ? 'is-invalid' : ''); ?>"
            aria-invalid="<?php echo e($errors->has('login') ? 'true' : 'false'); ?>"
            aria-describedby="<?php echo e($errors->has('login') ? 'login-error' : ''); ?>"
            placeholder="e.g. admin@example.com"
          >
          <?php $__errorArgs = ['login'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <p id="login-error" class="help-error"><?php echo e($message); ?></p>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="form-group stagger-3 fade-in-up">
          <label for="password" class="form-label">Password</label>
          <div class="relative">
            <input
              id="password"
              name="password"
              type="password"
              required
              autocomplete="current-password"
              class="input pr-20 <?php echo e($errors->has('password') ? 'is-invalid' : ''); ?>"
              aria-invalid="<?php echo e($errors->has('password') ? 'true' : 'false'); ?>"
              aria-describedby="<?php echo e($errors->has('password') ? 'password-error' : ''); ?>"
              placeholder="••••••••"
            >
            <button
              type="button"
              id="togglePassword"
              class="toggle-password"
              aria-label="Show password"
              aria-pressed="false"
            >Show</button>
          </div>
          <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <p id="password-error" class="help-error"><?php echo e($message); ?></p>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="flex items-center justify-between pt-2 stagger-4 fade-in-up">
          <label class="flex items-center gap-3 text-sm text-gray-700 cursor-pointer">
            <input type="checkbox" name="remember" value="1" class="checkbox-custom" <?php echo e(old('remember') ? 'checked' : ''); ?>>
            <span class="font-medium">Remember me</span>
          </label>
          <?php if(Route::has('password.request')): ?>
            <a href="<?php echo e(route('password.request')); ?>" class="text-sm link-primary">Forgot password?</a>
          <?php endif; ?>
        </div>

        
        <div class="pt-3 stagger-4 fade-in-up">
          <button type="submit" class="btn-primary w-full py-4 text-base font-semibold rounded-xl">
            Sign in
          </button>
        </div>
      </form>
    </div>

    <!-- Register Section -->
    <div id="registerSection" style="display: none;">
      <h1 class="title stagger-1 fade-in-up">Create account</h1>

      <?php if($errors->any()): ?>
        <div class="error-alert stagger-2 fade-in-up" role="alert">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div><?php echo e($error); ?></div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="<?php echo e(route('register.store')); ?>" id="register-form" class="space-y-6" novalidate>
        <?php echo csrf_field(); ?>

        
        <div class="form-group stagger-3 fade-in-up">
          <label for="reg-name" class="form-label">Name</label>
          <input
            id="reg-name"
            name="name"
            type="text"
            required
            autocomplete="name"
            class="input"
            placeholder="Your full name"
            value="<?php echo e(old('name')); ?>"
          >
        </div>

        
        <div class="form-group stagger-3 fade-in-up">
          <label for="reg-email" class="form-label">Email</label>
          <input
            id="reg-email"
            name="email"
            type="email"
            required
            autocomplete="email"
            class="input"
            placeholder="e.g. user@example.com"
            value="<?php echo e(old('email')); ?>"
          >
        </div>

        
        <div class="form-group stagger-3 fade-in-up">
          <label for="reg-password" class="form-label">Password</label>
          <div class="relative">
            <input
              id="reg-password"
              name="password"
              type="password"
              required
              autocomplete="new-password"
              class="input pr-20"
              placeholder="••••••••"
            >
            <button
              type="button"
              id="toggleRegPassword"
              class="toggle-password"
              aria-label="Show password"
              aria-pressed="false"
            >Show</button>
          </div>
        </div>


        
        <div class="form-group stagger-3 fade-in-up">
          <label for="reg-role" class="form-label">Role</label>
          <select id="reg-role" name="role" class="input" required>
            <option value="user" <?php echo e(old('role', 'user')==='user' ? 'selected' : ''); ?>>User</option>
            <option value="support" <?php echo e(old('role')==='support' ? 'selected' : ''); ?>>Support</option>
          </select>
          <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="help-error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        
        <div class="pt-3 stagger-4 fade-in-up">
          <button type="submit" class="btn-primary w-full py-4 text-base font-semibold rounded-xl">
            Create account
          </button>
        </div>
      </form>
    </div>

  </main>

  <script>
    // Tab switching
    (function () {
      const loginTab = document.getElementById('loginTab');
      const registerTab = document.getElementById('registerTab');
      const loginSection = document.getElementById('loginSection');
      const registerSection = document.getElementById('registerSection');

      if (!loginTab || !registerTab || !loginSection || !registerSection) return;

      loginTab.addEventListener('click', () => {
        loginTab.classList.add('active');
        registerTab.classList.remove('active');
        loginSection.style.display = 'block';
        registerSection.style.display = 'none';
      });

      registerTab.addEventListener('click', () => {
        registerTab.classList.add('active');
        loginTab.classList.remove('active');
        registerSection.style.display = 'block';
        loginSection.style.display = 'none';
      });
    })();

    // Toggle password visibility for login
    (function () {
      const btn = document.getElementById('togglePassword');
      const input = document.getElementById('password');
      if (!btn || !input) return;

      btn.addEventListener('click', () => {
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        btn.textContent = isHidden ? 'Hide' : 'Show';
        btn.setAttribute('aria-pressed', String(isHidden));
        btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
      });
    })();

    // Toggle password visibility for register
    (function () {
      const btn = document.getElementById('toggleRegPassword');
      const input = document.getElementById('reg-password');
      if (!btn || !input) return;

      btn.addEventListener('click', () => {
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        btn.textContent = isHidden ? 'Hide' : 'Show';
        btn.setAttribute('aria-pressed', String(isHidden));
        btn.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
      });
    })();

    // Prevent double submit for login
    (function () {
      const form = document.getElementById('login-form');
      if (!form) return;
      form.addEventListener('submit', () => {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn && !submitBtn.disabled) {
          submitBtn.disabled = true;
          submitBtn.textContent = 'Signing in…';
        }
      });
    })();

    // Prevent double submit for register
    (function () {
      const form = document.getElementById('register-form');
      if (!form) return;
      form.addEventListener('submit', () => {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn && !submitBtn.disabled) {
          submitBtn.disabled = true;
          submitBtn.textContent = 'Creating account…';
        }
      });
    })();
  </script>
</body>
</html>
<?php /**PATH C:\Users\Gjlang\kl_guide_tracker\resources\views/auth/login.blade.php ENDPATH**/ ?>