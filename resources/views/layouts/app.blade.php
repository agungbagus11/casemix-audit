<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casemix Audit Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-100 via-blue-50 to-emerald-50 text-slate-800">
    <div class="min-h-screen">
        <nav class="sticky top-0 z-30 border-b border-white/50 bg-white/80 backdrop-blur-lg shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-slate-900">Casemix Audit Dashboard</h1>
                    <p class="text-sm text-slate-500">Workbench klaim, verifikasi, pending, dan review operasional</p>
                </div>

                <div class="flex items-center gap-3 flex-wrap justify-end">
                    @auth
                        <div class="text-right">
                            <div class="text-sm font-bold text-slate-900">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-slate-500">{{ auth()->user()->role }}</div>
                        </div>

                        <a href="{{ route('casemix.index') }}"
                           class="inline-flex items-center rounded-2xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-slate-800 transition">
                            Dashboard
                        </a>

                        <a href="{{ route('profile.show') }}"
                           class="inline-flex items-center rounded-2xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-emerald-700 transition">
                            Profil
                        </a>

                        @role('admin', 'casemix', 'manager')
                            <a href="{{ route('activity.index') }}"
                               class="inline-flex items-center rounded-2xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-violet-700 transition">
                                Activity
                            </a>
                        @endrole

                        @role('admin')
                            <a href="{{ route('users.index') }}"
                               class="inline-flex items-center rounded-2xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 transition">
                                User
                            </a>
                        @endrole

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center rounded-2xl bg-rose-600 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-rose-700 transition">
                                Logout
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @if(session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-800 shadow-sm">
                    <div class="font-semibold">Berhasil</div>
                    <div class="text-sm mt-1">{{ session('success') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-800 shadow-sm">
                    <div class="font-semibold mb-2">Ada error</div>
                    <ul class="list-disc pl-5 text-sm space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>