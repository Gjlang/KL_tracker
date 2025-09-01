@php
  // Brand tokens
  $brand = '#22255b';   // primary
  $focus = '#4bbbed';   // focus ring
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login • {{ config('app.name', 'App') }}</title>
  @vite(['resources/css/app.css'])
  <style>
    :root { --brand: {{ $brand }}; --focus: {{ $focus }}; }
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    .btn-primary { background: var(--brand); color: #fff; transition: filter .15s ease; }
    .btn-primary:hover { filter: brightness(.95); }
    .btn-primary:disabled { opacity: .6; cursor: not-allowed; }

    .input {
      width: 100%;
      border: 1px solid #d1d5db;
      background: #fff;
      border-radius: .5rem;
      padding: .75rem 1rem;
      font-size: 1rem;
      line-height: 1.5rem;
      transition: box-shadow .15s ease, border-color .15s ease;
    }
    .input:focus {
      outline: none;
      border-color: var(--focus);
      box-shadow: 0 0 0 3px color-mix(in srgb, var(--focus) 25%, transparent);
    }
    .input.is-invalid { border-color: #ef4444; }
    .help-error { color: #b91c1c; font-size: .875rem; margin-top: .375rem; }

    /* Keep autofill clean */
    input:-webkit-autofill {
      -webkit-box-shadow: 0 0 0 1000px #fff inset;
      -webkit-text-fill-color: #111827;
    }
  </style>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
  <main class="w-full max-w-md bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
    <h1 class="text-2xl font-semibold text-center mb-8" style="color: var(--brand);">Sign in</h1>

    @if ($errors->any())
      <div class="mb-6 p-4 border border-red-200 bg-red-50 text-red-700 text-sm rounded-lg" role="alert">
        @foreach ($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}" id="login-form" class="space-y-5" novalidate>
      @csrf

      {{-- Username / Email --}}
      <div>
        <label for="login" class="block text-sm font-medium text-gray-700 mb-1">Email or username</label>
        <input
          id="login"
          name="login"
          type="text"
          required
          autocomplete="username"
          autofocus
          value="{{ old('login') }}"
          class="input {{ $errors->has('login') ? 'is-invalid' : '' }}"
          aria-invalid="{{ $errors->has('login') ? 'true' : 'false' }}"
          aria-describedby="{{ $errors->has('login') ? 'login-error' : '' }}"
          placeholder="e.g. admin@example.com"
        >
        @error('login')
          <p id="login-error" class="help-error">{{ $message }}</p>
        @enderror
      </div>

      {{-- Password --}}
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <div class="relative">
          <input
            id="password"
            name="password"
            type="password"
            required
            autocomplete="current-password"
            class="input pr-16 {{ $errors->has('password') ? 'is-invalid' : '' }}"
            aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
            aria-describedby="{{ $errors->has('password') ? 'password-error' : '' }}"
            placeholder="••••••••"
          >
          <button
            type="button"
            id="togglePassword"
            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-sm text-gray-500 hover:text-gray-700 px-2 py-1"
            aria-label="Show password"
            aria-pressed="false"
          >Show</button>
        </div>
        @error('password')
          <p id="password-error" class="help-error">{{ $message }}</p>
        @enderror
      </div>

      {{-- Remember / Forgot --}}
      <div class="flex items-center justify-between pt-1">
        <label class="flex items-center gap-2 text-sm text-gray-700">
          <input type="checkbox" name="remember" value="1" class="w-4 h-4 rounded" style="accent-color: var(--brand);" {{ old('remember') ? 'checked' : '' }}>
          Remember me
        </label>
        @if (Route::has('password.request'))
          <a href="{{ route('password.request') }}" class="text-sm text-[#4bbbed] hover:underline">Forgot password?</a>
        @endif
      </div>

      {{-- Submit --}}
      <div class="pt-1">
        <button type="submit" class="btn-primary w-full py-3 text-base font-medium rounded-lg">
          Sign in
        </button>
      </div>
    </form>
  </main>

  <script>
    // Toggle password visibility with a11y
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

    // Prevent double submit + subtle loading state
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
  </script>
</body>
</html>
