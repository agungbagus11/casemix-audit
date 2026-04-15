<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casemix Audit Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .glass {
            background: rgba(255,255,255,0.78);
            backdrop-filter: blur(10px);
        }
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

                <div class="flex items-center gap-2">
                    <a href="{{ route('casemix.index') }}"
                       class="inline-flex items-center rounded-2xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white shadow hover:bg-slate-800 transition">
                        Dashboard
                    </a>
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
</body>
</html>