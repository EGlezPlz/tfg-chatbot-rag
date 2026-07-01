<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VenancIA Admin — Acceso</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: '#174e96' } } } }</script>
</head>
<body class="min-h-screen flex">

    <!-- Lado izquierdo — imagen -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
        <img src="/images/ies-fachada.png"
            alt="IES Venancio Blanco"
            class="absolute inset-0 w-full h-full object-cover">
        <!-- Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-[#54595F]/85 to-[#0d2e5a]/70"></div>
        <!-- Contenido sobre la imagen -->
        <div class="relative z-10 flex flex-col justify-between p-12 w-full">
            <div>
                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h2 class="text-white text-2xl font-bold mt-6">VenancIA Admin</h2>
                <p class="text-white/70 text-sm mt-2">Panel de gestión del corpus documental</p>
            </div>

            <div>
                <blockquote class="text-white/90 text-lg font-light leading-relaxed mb-6">
                    "El conocimiento es la base de una educación de calidad. VenancIA pone la inteligencia artificial al servicio de la comunidad educativa."
                </blockquote>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-white text-sm font-medium">IES Venancio Blanco</p>
                        <p class="text-white/60 text-xs">Salamanca · Curso 2025/2026</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lado derecho — formulario -->
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-gray-50 p-8">
        <div class="w-full max-w-sm">

            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900">Bienvenido</h1>
                <p class="text-gray-500 text-sm mt-1">Accede con tus credenciales de administrador</p>
            </div>

            @if(session('error'))
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-red-600 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="/login" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Usuario</label>
                    <input type="text" name="username" value="admin"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#174e96] focus:border-transparent bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
                    <input type="password" name="password"
                        class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#174e96] focus:border-transparent bg-white"
                        placeholder="••••••••">
                </div>
                <button type="submit"
                    class="w-full bg-[#174e96] text-white py-2.5 rounded-lg text-sm font-medium hover:bg-[#0d3a75] transition-colors mt-2">
                    Iniciar sesión
                </button>
            </form>

            <p class="text-center text-xs text-gray-400 mt-8">
                Sistema de gestión interno · Solo personal autorizado
            </p>
        </div>
    </div>

</body>
</html>