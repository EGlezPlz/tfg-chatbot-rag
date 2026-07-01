<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VenancIA Admin — Corpus</title>
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
            <a href="/dashboard" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 text-sm font-medium transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
            <a href="/corpus" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-blue-50 text-primary text-sm font-medium">
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
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Gestión del corpus</h1>
                <p class="text-gray-500 text-sm mt-1">Fuentes documentales indexadas en Qdrant</p>
            </div>
            <button onclick="document.getElementById('modal').classList.remove('hidden')"
                class="flex items-center gap-2 px-4 py-2.5 bg-primary text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Añadir fuente
            </button>
        </div>

        <!-- Summary -->
        <div class="grid grid-cols-4 gap-4 mb-6">
            @foreach([
                ['380', 'Chunks totales', 'blue'],
                ['327', 'Web IES', 'purple'],
                ['53', 'Educacyl (JCyL)', 'green'],
                ['3', 'PDFs institucionales', 'orange'],
            ] as [$num, $label, $color])
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <p class="text-2xl font-bold text-gray-900">{{ $num }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $label }}</p>
            </div>
            @endforeach
        </div>

        <!-- Documents table -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-900">Fuentes indexadas</h2>
                <span class="text-xs text-gray-400">Última actualización: hoy</span>
            </div>
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500">Documento</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500">Tipo</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500">Origen</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500">Chunks</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500">Estado</th>
                        <th class="text-left px-6 py-3 text-xs font-medium text-gray-500">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach([
                        ['Web IES Venancio Blanco', 'HTML', 'ies', '58', 'Activo'],
                        ['Propuesta Curricular ESO 2025-26', 'PDF', 'ies', '95', 'Activo'],
                        ['Propuesta Curricular Bachillerato 2025-26', 'PDF', 'ies', '69', 'Activo'],
                        ['Reglamento de Régimen Interior', 'PDF', 'ies', '105', 'Activo'],
                        ['Portal Educacyl — Junta CyL', 'HTML', 'jcyl', '53', 'Activo'],
                    ] as [$name, $type, $origen, $chunks, $status])
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $type === 'PDF' ? 'bg-red-50' : 'bg-blue-50' }}">
                                    <svg class="w-4 h-4 {{ $type === 'PDF' ? 'text-red-500' : 'text-primary' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs px-2 py-1 rounded-full {{ $type === 'PDF' ? 'bg-red-50 text-red-600' : 'bg-blue-50 text-primary' }}">
                                {{ $type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $origen }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $chunks }}</td>
                        <td class="px-6 py-4">
                            <span class="text-xs bg-green-50 text-green-700 px-2 py-1 rounded-full">● {{ $status }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <button class="text-xs text-primary hover:underline">Re-indexar</button>
                                <span class="text-gray-300">·</span>
                                <button class="text-xs text-red-500 hover:underline">Eliminar</button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal añadir fuente — Wizard 3 pasos -->
    <div id="modal" class="hidden fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-lg mx-4">

            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-gray-900">Añadir nueva fuente</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Indicador de pasos -->
            <div class="flex items-center mb-8">
                <div class="flex items-center">
                    <div id="step-dot-1" class="w-8 h-8 rounded-full bg-primary text-white text-xs flex items-center justify-center font-medium">1</div>
                    <span class="ml-2 text-xs font-medium text-primary">Datos</span>
                </div>
                <div class="flex-1 h-px bg-gray-200 mx-3"></div>
                <div class="flex items-center">
                    <div id="step-dot-2" class="w-8 h-8 rounded-full bg-gray-200 text-gray-400 text-xs flex items-center justify-center font-medium">2</div>
                    <span id="step-label-2" class="ml-2 text-xs font-medium text-gray-400">Validación</span>
                </div>
                <div class="flex-1 h-px bg-gray-200 mx-3"></div>
                <div class="flex items-center">
                    <div id="step-dot-3" class="w-8 h-8 rounded-full bg-gray-200 text-gray-400 text-xs flex items-center justify-center font-medium">3</div>
                    <span id="step-label-3" class="ml-2 text-xs font-medium text-gray-400">Confirmar</span>
                </div>
            </div>

            <!-- PASO 1 -->
            <div id="step-1">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del documento</label>
                        <input id="doc-nombre" type="text" placeholder="Ej: Calendario Escolar 2026-27"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
                            <select id="doc-tipo" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white">
                                <option>PDF</option>
                                <option>HTML (URL)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Origen</label>
                            <select id="doc-origen" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary bg-white">
                                <option>ies</option>
                                <option>jcyl</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Archivo o URL</label>
                        <input id="doc-url" type="text" placeholder="URL o ruta del fichero"
                            class="w-full px-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                </div>
                <div class="flex gap-3 mt-6">
                    <button onclick="closeModal()" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button onclick="goToStep2()" class="flex-1 px-4 py-2.5 bg-primary text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                        Siguiente →
                    </button>
                </div>
            </div>

            <!-- PASO 2 -->
            <div id="step-2" class="hidden">
                <div id="validating" class="text-center py-6">
                    <div class="w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                    <p class="text-sm text-gray-500">Analizando documento...</p>
                </div>
                <div id="validation-result" class="hidden space-y-3">
                    <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg border border-green-100">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-green-800">Texto extraíble correctamente</p>
                            <p class="text-xs text-green-600">El documento contiene texto seleccionable</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg border border-green-100">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-green-800">42 chunks generados</p>
                            <p class="text-xs text-green-600">Longitud media: 312 caracteres · 0 chunks rechazados</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg border border-green-100">
                        <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-green-800">Metadatos completos</p>
                            <p class="text-xs text-green-600">tipo · origen · documento correctamente asignados</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <p class="text-xs font-medium text-gray-500 mb-2">PREVISUALIZACIÓN — primeros chunks</p>
                        <div class="space-y-2 max-h-32 overflow-y-auto">
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                                <p class="text-xs text-gray-600">"El calendario escolar 2026-27 establece el inicio del curso el 10 de septiembre de 2026 para Educación Secundaria Obligatoria y Bachillerato..."</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                                <p class="text-xs text-gray-600">"Las vacaciones de Navidad comprenden desde el 23 de diciembre de 2026 hasta el 7 de enero de 2027, ambos inclusive..."</p>
                            </div>
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-100">
                                <p class="text-xs text-gray-600">"La evaluación final ordinaria de 2.º de Bachillerato tendrá lugar entre el 1 y el 5 de junio de 2027..."</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="validation-buttons" class="hidden flex gap-3 mt-6">
                    <button onclick="goToStep1()" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                        ← Atrás
                    </button>
                    <button onclick="goToStep3()" class="flex-1 px-4 py-2.5 bg-primary text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                        Confirmar →
                    </button>
                </div>
            </div>

            <!-- PASO 3 -->
            <div id="step-3" class="hidden">
                <div id="indexing" class="hidden text-center py-6">
                    <div class="w-12 h-12 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
                    <p class="text-sm text-gray-500">Indexando en Qdrant...</p>
                    <p class="text-xs text-gray-400 mt-1">Generando embeddings con nomic-embed-text</p>
                </div>
                <div id="confirm-content">
                    <div class="p-4 bg-blue-50 rounded-xl border border-blue-100 mb-6">
                        <p class="text-sm font-medium text-gray-900 mb-3">Resumen de la operación</p>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Documento</span>
                                <span id="confirm-nombre" class="font-medium text-gray-900">—</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Tipo</span>
                                <span id="confirm-tipo" class="font-medium text-gray-900">—</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Origen</span>
                                <span id="confirm-origen" class="font-medium text-gray-900">—</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Chunks a indexar</span>
                                <span class="font-medium text-primary">42 nuevos chunks</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Total tras indexar</span>
                                <span class="font-medium text-gray-900">422 chunks en corpus_centro</span>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mb-6">El proceso de vectorización puede tardar entre 30 segundos y 2 minutos dependiendo del tamaño del documento.</p>
                    <div class="flex gap-3">
                        <button onclick="goToStep2()" class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition-colors">
                            ← Atrás
                        </button>
                        <button onclick="startIndexing()" class="flex-1 px-4 py-2.5 bg-primary text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                            Indexar en Qdrant
                        </button>
                    </div>
                </div>
                <div id="success" class="hidden text-center py-4">
                    <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <p class="text-base font-bold text-gray-900 mb-1">Indexación completada</p>
                    <p class="text-sm text-gray-500 mb-1">42 chunks añadidos a corpus_centro</p>
                    <p class="text-xs text-gray-400 mb-6">El chatbot ya puede responder consultas sobre este documento</p>
                    <button onclick="closeModal()" class="w-full px-4 py-2.5 bg-primary text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function closeModal() {
        document.getElementById('modal').classList.add('hidden');
        resetWizard();
    }

    function resetWizard() {
        goToStep1();
        document.getElementById('doc-nombre').value = '';
        document.getElementById('doc-url').value = '';
        document.getElementById('validation-result').classList.add('hidden');
        document.getElementById('validation-buttons').classList.add('hidden');
        document.getElementById('validating').classList.remove('hidden');
        document.getElementById('confirm-content').classList.remove('hidden');
        document.getElementById('success').classList.add('hidden');
        document.getElementById('indexing').classList.add('hidden');
    }

    function setStep(n) {
        [1,2,3].forEach(i => {
            document.getElementById('step-' + i).classList.add('hidden');
            document.getElementById('step-dot-' + i).className = 'w-8 h-8 rounded-full bg-gray-200 text-gray-400 text-xs flex items-center justify-center font-medium';
            if (i > 1) document.getElementById('step-label-' + i).className = 'ml-2 text-xs font-medium text-gray-400';
        });
        document.getElementById('step-' + n).classList.remove('hidden');
        document.getElementById('step-dot-' + n).className = 'w-8 h-8 rounded-full bg-primary text-white text-xs flex items-center justify-center font-medium';
        if (n > 1) document.getElementById('step-label-' + n).className = 'ml-2 text-xs font-medium text-primary';
    }

    function goToStep1() { setStep(1); }

    function goToStep2() {
        const nombre = document.getElementById('doc-nombre').value.trim();
        if (!nombre) {
            alert('Por favor introduce el nombre del documento');
            return;
        }
        setStep(2);
        document.getElementById('validating').classList.remove('hidden');
        document.getElementById('validation-result').classList.add('hidden');
        document.getElementById('validation-buttons').classList.add('hidden');
        setTimeout(() => {
            document.getElementById('validating').classList.add('hidden');
            document.getElementById('validation-result').classList.remove('hidden');
            document.getElementById('validation-buttons').classList.remove('hidden');
        }, 2000);
    }

    function goToStep3() {
        setStep(3);
        document.getElementById('confirm-nombre').textContent = document.getElementById('doc-nombre').value;
        document.getElementById('confirm-tipo').textContent = document.getElementById('doc-tipo').value;
        document.getElementById('confirm-origen').textContent = document.getElementById('doc-origen').value;
    }

    function startIndexing() {
        document.getElementById('confirm-content').classList.add('hidden');
        document.getElementById('indexing').classList.remove('hidden');
        setTimeout(() => {
            document.getElementById('indexing').classList.add('hidden');
            document.getElementById('success').classList.remove('hidden');
        }, 3000);
    }
    </script>

</body>
</html>