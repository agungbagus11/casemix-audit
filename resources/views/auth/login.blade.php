<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Casemix Audit</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-100 via-blue-50 to-emerald-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md rounded-[28px] bg-white shadow-xl border border-slate-200 p-8">
        <div class="mb-6 text-center">
            <h1 class="text-3xl font-extrabold text-slate-900">Casemix Audit</h1>
            <p class="text-sm text-slate-500 mt-2">Silakan login untuk masuk ke dashboard</p>
        </div>

        @if($errors->any())
            <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       class="w-full rounded-2xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-slate-400"
                       placeholder="email@casemix.local"
                       required>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                <input type="password"
                       name="password"
                       class="w-full rounded-2xl border border-slate-300 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-slate-400"
                       placeholder="password"
                       required>
            </div>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="remember" value="1" class="rounded border-slate-300">
                Remember me
            </label>

            <button type="submit"
                    class="w-full rounded-2xl bg-slate-900 px-5 py-3 font-bold text-white hover:bg-slate-800 transition">
                Login
            </button>
        </form>

        <div class="mt-6 rounded-2xl bg-slate-50 border border-slate-200 p-4 text-xs text-slate-600">
            <div class="font-bold mb-2">Akun demo</div>
            <div>admin@casemix.local / password</div>
            <div>casemix@casemix.local / password</div>
            <div>verifier@casemix.local / password</div>
            <div>manager@casemix.local / password</div>
        </div>
    </div>
</body>
</html>