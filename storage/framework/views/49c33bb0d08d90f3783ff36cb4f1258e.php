<?php
  // Brand tokens - keeping your existing values
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
  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --brand: <?php echo e($brand); ?>;
      --focus: <?php echo e($focus); ?>;
      --destructive: #d33831;
      --paper: #F7F7F9;
      --surface: #FFFFFF;
      --ink: #1C1E26;
      --hairline: #EAEAEA;
      --neutral-300: #d1d5db;
      --neutral-400: #9ca3af;
      --neutral-500: #6b7280;
      --neutral-600: #6b7280;
      --neutral-700: #374151;
    }

    body {
      font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
      background: linear-gradient(135deg, var(--paper) 0%, #f0f0f2 100%);
      color: var(--ink);
      line-height: 1.6;
      min-height: 100vh;
    }

    .font-serif {
      font-family: 'EB Garamond', Georgia, serif;
    }

    /* Utilities */
    .hairline {
      border-width: 1px;
      border-color: var(--hairline);
    }

    .caps-label {
      letter-spacing: 0.06em;
      text-transform: uppercase;
      font-size: 11px;
      color: var(--neutral-600);
    }

    .tabular-nums {
      font-variant-numeric: tabular-nums;
    }

    /* Auth card */
    .auth-card {
      position: relative;
      background: var(--surface);
      border-radius: 1rem;
      border: 1px solid rgba(209, 213, 219, 0.7);
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
      max-width: 28rem;
      width: 100%;
      padding: 1.5rem;
      padding-top: 3.5rem; /* Extra space for overlapping badge */
    }

    @media (min-width: 768px) {
      .auth-card {
        padding: 2rem;
        padding-top: 4rem;
      }
    }

    /* Logo badge */
    .logo-badge {
      position: absolute;
      top: -2.5rem;
      left: 0;
      right: 0;
      margin: 0 auto;
      width: 5rem;
      height: 5rem;
      border-radius: 50%;
      background: var(--surface);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      border: 4px solid var(--surface);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 10;
    }

    .logo-badge img {
      width: 3rem;
      height: 3rem;
      object-fit: contain;
    }

    /* Tabs */
    .tab-container {
      display: flex;
      margin-bottom: 2rem;
      border-bottom: 1px solid var(--hairline);
      gap: 2rem;
      justify-content: center;
    }

    .tab-button {
      padding: 0.75rem 0;
      background: none;
      border: none;
      color: var(--neutral-600);
      font-weight: 500;
      cursor: pointer;
      position: relative;
      transition: color 150ms ease;
      font-size: 0.875rem;
    }

    .tab-button:hover {
      color: var(--ink);
    }

    .tab-button.active {
      color: var(--brand);
    }

    .tab-button.active::after {
      content: '';
      position: absolute;
      bottom: -1px;
      left: 0;
      right: 0;
      height: 2px;
      background: var(--brand);
    }

    /* Typography */
    .section-title {
      font-size: 1.875rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
      color: var(--ink);
      text-align: center;
    }

    .section-subtitle {
      font-size: 0.875rem;
      color: var(--neutral-600);
      margin-bottom: 2rem;
      text-align: center;
    }

    /* Form elements */
    .form-group {
      margin-bottom: 1.25rem;
    }

    .form-label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: 500;
      color: var(--ink);
      font-size: 0.875rem;
    }

    .form-input-container {
      position: relative;
    }

    .form-input-icon {
      position: absolute;
      inset-y: 0;
      left: 0;
      display: flex;
      align-items: center;
      padding-left: 0.75rem;
      pointer-events: none;
    }

    .form-input-icon svg {
      width: 1rem;
      height: 1rem;
      color: var(--neutral-400);
    }

    .form-input {
      width: 100%;
      height: 2.75rem;
      padding-left: 2.25rem;
      padding-right: 1rem;
      border: 1px solid var(--neutral-300);
      border-radius: 0.75rem;
      background: var(--surface);
      color: var(--ink);
      font-size: 0.875rem;
      transition: all 150ms ease;
    }

    .form-input:focus {
      outline: none;
      border-color: transparent;
      box-shadow: 0 0 0 2px var(--focus);
    }

    .form-input.is-invalid {
      border-color: var(--destructive);
    }

    .form-input.is-invalid:focus {
      box-shadow: 0 0 0 2px rgba(211, 56, 49, 0.2);
    }

    /* Password toggle */
    .password-container {
      position: relative;
    }

    .password-toggle {
      position: absolute;
      right: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: var(--neutral-500);
      font-size: 0.75rem;
      font-weight: 500;
      cursor: pointer;
      padding: 0.25rem 0.5rem;
      border-radius: 0.375rem;
      transition: color 150ms ease;
    }

    .password-toggle:hover {
      color: var(--brand);
    }

    .password-input {
      padding-right: 4rem;
    }

    /* Checkbox */
    .checkbox-container {
      display: flex;
      align-items: center;
      gap: 0.75rem;
    }

    .checkbox-input {
      width: 1rem;
      height: 1rem;
      accent-color: var(--brand);
    }

    .checkbox-label {
      font-size: 0.875rem;
      color: var(--neutral-700);
      cursor: pointer;
    }

    /* Options row */
    .options-row {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-top: 0.75rem;
      margin-bottom: 1.25rem;
    }

    /* Links */
    .link-subtle {
      color: var(--focus);
      text-decoration: none;
      font-size: 0.875rem;
      font-weight: 500;
      transition: color 150ms ease;
    }

    .link-subtle:hover {
      color: var(--brand);
    }

    .link-primary {
      color: var(--focus);
      text-decoration: none;
      font-weight: 500;
      transition: color 150ms ease;
    }

    .link-primary:hover {
      color: var(--brand);
    }

    /* Buttons */
    .btn-primary {
      width: 100%;
      height: 2.75rem;
      background: var(--brand);
      color: white;
      border: none;
      border-radius: 9999px;
      font-weight: 600;
      font-size: 0.875rem;
      cursor: pointer;
      transition: all 150ms ease;
      position: relative;
      overflow: hidden;
      margin-bottom: 1.5rem;
    }

    .btn-primary:hover:not(:disabled) {
      opacity: 0.9;
      transform: translateY(-0.5px);
    }

    .btn-primary:focus {
      outline: none;
      box-shadow: 0 0 0 2px var(--focus);
    }

    .btn-primary:disabled {
      opacity: 0.6;
      cursor: not-allowed;
      transform: none;
    }

    .btn-primary[aria-busy="true"] {
      color: transparent;
    }

    .btn-primary[aria-busy="true"]::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 1rem;
      height: 1rem;
      margin: -0.5rem 0 0 -0.5rem;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-top: 2px solid white;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Error handling */
    .error-alert {
      background: rgba(211, 56, 49, 0.05);
      border: 1px solid rgba(211, 56, 49, 0.2);
      color: var(--destructive);
      padding: 1rem;
      border-radius: 0.5rem;
      font-size: 0.875rem;
      margin-bottom: 1.5rem;
    }

    .field-error {
      color: var(--destructive);
      font-size: 0.75rem;
      margin-top: 0.25rem;
    }

    /* Footer link */
    .footer-text {
      text-align: center;
      font-size: 0.875rem;
      color: var(--neutral-600);
    }

    /* Responsive adjustments */
    @media (max-width: 640px) {
      .auth-card {
        margin: 1rem;
        padding: 1.5rem;
        padding-top: 3.5rem;
      }

      .options-row {
        flex-direction: column;
        gap: 0.75rem;
        align-items: flex-start;
      }

      .tab-container {
        gap: 1rem;
      }
    }

    /* Reduced motion */
    @media (prefers-reduced-motion: reduce) {
      * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
      }
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
  <!-- Auth Card with Overlapping Logo Badge -->
  <main class="auth-card">
    <!-- Logo Badge -->
    <div class="logo-badge">
      <img src="<?php echo e(asset('bluedale.png')); ?>" alt="Brand">
    </div>

    <!-- Tabs -->
    <div class="tab-container">
      <button type="button" id="loginTab" class="tab-button active">Sign in</button>
      <button type="button" id="registerTab" class="tab-button">Create account</button>
    </div>

    <!-- Login Section -->
    <div id="loginSection">
      <h1 class="section-title font-serif">Welcome back</h1>
      <p class="section-subtitle">Enter your credentials to continue</p>

      <?php if($errors->any()): ?>
        <div class="error-alert" role="alert">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div><?php echo e($error); ?></div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="<?php echo e(route('login.store')); ?>" id="login-form" novalidate x-data="{ loading: false }" @submit="loading = true">
        <?php echo csrf_field(); ?>

        <!-- Email/Username Field -->
        <div class="form-group">
          <label for="login" class="form-label">Email or username</label>
          <div class="form-input-container">
            <span class="form-input-icon">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </span>
            <input
              id="login"
              name="login"
              type="text"
              required
              autocomplete="username"
              autofocus
              value="<?php echo e(old('login')); ?>"
              class="form-input <?php echo e($errors->has('login') ? 'is-invalid' : ''); ?>"
              aria-invalid="<?php echo e($errors->has('login') ? 'true' : 'false'); ?>"
              aria-describedby="<?php echo e($errors->has('login') ? 'login-error' : ''); ?>"
              placeholder="Enter your email or username"
            >
          </div>
          <?php $__errorArgs = ['login'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <p id="login-error" class="field-error"><?php echo e($message); ?></p>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <!-- Password Field -->
        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <div class="form-input-container password-container" x-data="{ show: false }">
            <span class="form-input-icon">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
            </span>
            <input
              id="password"
              name="password"
              :type="show ? 'text' : 'password'"
              required
              autocomplete="current-password"
              class="form-input password-input <?php echo e($errors->has('password') ? 'is-invalid' : ''); ?>"
              aria-invalid="<?php echo e($errors->has('password') ? 'true' : 'false'); ?>"
              aria-describedby="<?php echo e($errors->has('password') ? 'password-error' : ''); ?>"
              placeholder="Enter your password"
            >
            <button
              type="button"
              @click="show = !show"
              class="password-toggle"
              :aria-label="show ? 'Hide password' : 'Show password'"
              :aria-pressed="show.toString()"
              x-text="show ? 'Hide' : 'Show'"
            >Show</button>
          </div>
          <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
            <p id="password-error" class="field-error"><?php echo e($message); ?></p>
          <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <!-- Options Row -->
        <div class="options-row">
          <label class="checkbox-container">
            <input type="checkbox" name="remember" value="1" class="checkbox-input" <?php echo e(old('remember') ? 'checked' : ''); ?>>
            <span class="checkbox-label">Remember me</span>
          </label>
          <?php if(Route::has('password.request')): ?>
            <a href="<?php echo e(route('password.request')); ?>" class="link-subtle">Forgot password?</a>
          <?php endif; ?>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          class="btn-primary"
          :disabled="loading"
          :aria-busy="loading.toString()"
          x-text="loading ? 'Signing in...' : 'Sign in'"
        >
          Sign in
        </button>

        <!-- Footer Text -->
        <div class="footer-text">
          Don't have an account? <a href="#" @click="$dispatch('switch-tab', 'register')" class="link-primary">Sign up</a>
        </div>
      </form>
    </div>

    <!-- Register Section -->
    <div id="registerSection" style="display: none;">
      <h1 class="section-title font-serif">Create account</h1>
      <p class="section-subtitle">Join us today</p>

      <?php if($errors->any()): ?>
        <div class="error-alert" role="alert">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div><?php echo e($error); ?></div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="<?php echo e(route('register.store')); ?>" id="register-form" novalidate x-data="{ loading: false }" @submit="loading = true">
        <?php echo csrf_field(); ?>

        <!-- Name Field -->
        <div class="form-group">
          <label for="reg-name" class="form-label">Name</label>
          <div class="form-input-container">
            <span class="form-input-icon">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
              </svg>
            </span>
            <input
              id="reg-name"
              name="name"
              type="text"
              required
              autocomplete="name"
              class="form-input"
              placeholder="Your full name"
              value="<?php echo e(old('name')); ?>"
            >
          </div>
        </div>

        <!-- Email Field -->
        <div class="form-group">
          <label for="reg-email" class="form-label">Email</label>
          <div class="form-input-container">
            <span class="form-input-icon">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            </span>
            <input
              id="reg-email"
              name="email"
              type="email"
              required
              autocomplete="email"
              class="form-input"
              placeholder="Your email address"
              value="<?php echo e(old('email')); ?>"
            >
          </div>
        </div>

        <!-- Password Field -->
        <div class="form-group">
          <label for="reg-password" class="form-label">Password</label>
          <div class="form-input-container password-container" x-data="{ show: false }">
            <span class="form-input-icon">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
              </svg>
            </span>
            <input
              id="reg-password"
              name="password"
              :type="show ? 'text' : 'password'"
              required
              autocomplete="new-password"
              class="form-input password-input"
              placeholder="Create a password"
            >
            <button
              type="button"
              @click="show = !show"
              class="password-toggle"
              :aria-label="show ? 'Hide password' : 'Show password'"
              :aria-pressed="show.toString()"
              x-text="show ? 'Hide' : 'Show'"
            >Show</button>
          </div>
        </div>

        <!-- Role Field -->
        <div class="form-group">
          <label for="reg-role" class="form-label">Role</label>
          <div class="form-input-container">
            <span class="form-input-icon">
              <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
            </span>
            <select id="reg-role" name="role" class="form-input" required style="padding-left: 2.25rem;">
              <option value="user" <?php echo e(old('role', 'user')==='user' ? 'selected' : ''); ?>>User</option>
              <option value="support" <?php echo e(old('role')==='support' ? 'selected' : ''); ?>>Support</option>
            </select>
          </div>
          <?php $__errorArgs = ['role'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="field-error"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          class="btn-primary"
          :disabled="loading"
          :aria-busy="loading.toString()"
          x-text="loading ? 'Creating account...' : 'Create account'"
        >
          Create account
        </button>

        <!-- Footer Text -->
        <div class="footer-text">
          Already have an account? <a href="#" @click="$dispatch('switch-tab', 'login')" class="link-primary">Sign in</a>
        </div>
      </form>
    </div>
  </main>

  <script>
    // Tab switching functionality
    document.addEventListener('alpine:init', () => {
      // Listen for custom tab switch events
      document.addEventListener('switch-tab', (e) => {
        if (e.detail === 'register') {
          switchToRegister();
        } else {
          switchToLogin();
        }
      });
    });

    function switchToLogin() {
      const loginTab = document.getElementById('loginTab');
      const registerTab = document.getElementById('registerTab');
      const loginSection = document.getElementById('loginSection');
      const registerSection = document.getElementById('registerSection');

      loginTab.classList.add('active');
      registerTab.classList.remove('active');
      loginSection.style.display = 'block';
      registerSection.style.display = 'none';
    }

    function switchToRegister() {
      const loginTab = document.getElementById('loginTab');
      const registerTab = document.getElementById('registerTab');
      const loginSection = document.getElementById('loginSection');
      const registerSection = document.getElementById('registerSection');

      registerTab.classList.add('active');
      loginTab.classList.remove('active');
      registerSection.style.display = 'block';
      loginSection.style.display = 'none';
    }

    // Tab click handlers
    document.getElementById('loginTab')?.addEventListener('click', switchToLogin);
    document.getElementById('registerTab')?.addEventListener('click', switchToRegister);
  </script>
</body>
</html>
<?php /**PATH D:\Projects\Laravel\KL_tracker\resources\views/auth/login.blade.php ENDPATH**/ ?>