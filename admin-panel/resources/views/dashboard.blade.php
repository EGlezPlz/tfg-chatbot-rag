<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VenancIA Admin — Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: '#174e96' } } } }</script>
</head>
<body class="min-h-screen bg-gray-50">

    <!-- Sidebar -->
    <div class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 flex flex-col">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-primary rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900">VenancIA Admin</p>
                    <p class="text-xs text-gray-400">IES Venancio Blanco</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 p-4 space-y-1">
            <a href="/dashboard" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-blue-50 text-primary text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
            <a href="/corpus" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Gestión del corpus
            </a>
        </nav>

        <div class="p-4 border-t border-gray-100">
            <div class="relative">
                <button onclick="this.nextElementSibling.classList.toggle('hidden')"
                    class="flex items-center gap-3 w-full px-3 py-2.5 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-primary text-white text-xs font-bold flex items-center justify-center flex-shrink-0">
                        A
                    </div>
                    <div class="text-left min-w-0">
                        <p class="text-xs font-medium text-gray-900">admin</p>
                        <p class="text-xs text-gray-400 truncate">Administrador IES</p>
                    </div>
                </button>
                <div class="hidden absolute bottom-full left-0 mb-2 w-full bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                    <form method="POST" action="/logout">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2.5 text-xs text-red-600 hover:bg-red-50 transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="ml-64 p-8">
        <div class="mb-8">
            <h1 class="text-xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-500 text-sm mt-1">Estado del sistema VenancIA</p>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm text-gray-500">Chunks indexados</p>
                    <span class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2 1 3 3 3h10c2 0 3-1 3-3V7c0-2-1-3-3-3H7C5 4 4 5 4 7z"/>
                        </svg>
                    </span>
                </div>
                <p class="text-3xl font-bold text-gray-900">380</p>
                <p class="text-xs text-green-600 mt-1">● Corpus activo</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm text-gray-500">Fuentes documentales</p>
                    <span class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </span>
                </div>
                <p class="text-3xl font-bold text-gray-900">5</p>
                <p class="text-xs text-gray-400 mt-1">HTML + PDF</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-6">
                <div class="flex items-center justify-between mb-4">
                    <p class="text-sm text-gray-500">Última actualización</p>
                    <span class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                </div>
                <p class="text-3xl font-bold text-gray-900">Hoy</p>
                <p class="text-xs text-gray-400 mt-1">Mayo 2026</p>
            </div>
        </div>

        <!-- Services status -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6 mb-6">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">Estado de los servicios</h2>
            <div class="space-y-3">
                @foreach([
                    ['Qdrant', 'Base vectorial', 'Operativo', '380 vectores indexados'],
                    ['Ollama', 'Motor LLM', 'Operativo', 'llama3.2:latest cargado'],
                    ['n8n', 'Orquestador RAG', 'Operativo', 'Workflow activo'],
                    ['FastAPI', 'Backend API', 'Operativo', 'v0.4-chat-frontend'],
                ] as [$name, $desc, $status, $detail])
                <div class="flex items-center justify-between py-2 border-b border-gray-50 last:border-0">
                    <div class="flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $name }}</p>
                            <p class="text-xs text-gray-400">{{ $desc }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs bg-green-50 text-green-700 px-2 py-1 rounded-full">{{ $status }}</span>
                        <p class="text-xs text-gray-400 mt-1">{{ $detail }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Quick actions -->
        <div class="bg-white rounded-2xl border border-gray-200 p-6">
            <h2 class="text-sm font-semibold text-gray-900 mb-4">Acciones rápidas</h2>
            <div class="flex gap-3">
                <a href="/corpus" class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Actualizar corpus
                </a>
                <a href="/corpus" class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Ver documentos
                </a>
            </div>
        </div>
    </div>

</body>
</html>