<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DevTeams - Panel de Jueces</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Navegación -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-8">
                    <div class="flex items-center gap-2">
                        <div class="text-2xl text-purple-600">&lt;/&gt;</div>
                        <span class="text-xl font-bold">DevTeams</span>
                    </div>
                </div>
                
                <div class="flex items-center gap-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 text-gray-600 hover:text-gray-900">
                            <span>📋</span>
                            <span>Cerrar sesión</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Dashboard -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Dashboard</h1>
            <p class="text-gray-600">Gestiona tus equipos, eventos, invitaciones y código</p>
        </div>

        <!-- Navegación de pestañas (solo para Admin) -->
        @if($isAdmin)
            <div class="mb-8 bg-white rounded-full shadow-sm p-2 inline-flex gap-1 border border-gray-200">
                <a href="{{ route('admin.equipos.index') }}" class="px-6 py-2 rounded-full text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                    <span>👥</span>
                    <span>Equipos</span>
                </a>
                <a href="{{ route('admin.eventos.index') }}" class="px-6 py-2 rounded-full text-gray-600 hover:bg-gray-50 flex items-center gap-2">
                    <span>📅</span>
                    <span>Eventos</span>
                </a>
                <a href="{{ route('admin.jueces.index') }}" class="px-6 py-2 rounded-full bg-gray-200 text-gray-900 font-medium flex items-center gap-2">
                    <span>⚖️</span>
                    <span>Jueces</span>
                </a>
            </div>
        @endif

        <!-- Contenido principal -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Panel de Calificación de Jueces</h2>
            <p class="text-gray-600">Selecciona un evento activo y califica a los equipos participantes</p>
        </div>

        <!-- Selector de evento -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-3">Seleccionar Evento Activo</label>
            <select 
                id="eventoSelector"
                onchange="mostrarEquipos(this.value)"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-lg"
            >
                <option value="">-- Selecciona un evento --</option>
                <option value="1">Eventos y Retos - Hackathon (14 Dic - 16 Dic 2024)</option>
                <option value="2">Desarrollo Web Moderno - Concurso (20 Dic - 22 Dic 2024)</option>
            </select>
        </div>

        <!-- Contenedor de equipos (oculto inicialmente) -->
        <div id="equiposContainer" class="hidden">
            <!-- Información del evento seleccionado -->
            <div class="bg-gradient-to-r from-purple-600 to-blue-500 rounded-2xl p-6 text-white mb-6">
                <div class="flex items-center gap-3 mb-3">
                    <span class="bg-white/20 backdrop-blur-sm text-white text-sm px-4 py-1 rounded-full">
                        &lt;/&gt; hackathon
                    </span>
                    <span class="bg-green-500/90 text-white text-sm px-4 py-1 rounded-full">
                        ● Active
                    </span>
                </div>
                <h3 class="text-2xl font-bold mb-2">Eventos y Retos</h3>
                <p class="opacity-90">Desarrolla aplicaciones descentralizadas en 48 horas</p>
                <div class="flex items-center gap-4 mt-4 text-sm">
                    <div class="flex items-center gap-2">
                        <span>📅</span>
                        <span>14 Dic - 16 Dic 2024</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span>👥</span>
                        <span>5 equipos inscritos</span>
                    </div>
                </div>
            </div>

            <!-- Lista de equipos para calificar -->
            <div class="space-y-4">
                <!-- Equipo 1 -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-xl font-bold text-gray-900">Equipo 1</h3>
                                <span class="bg-blue-100 text-blue-600 text-xs px-3 py-1 rounded-full">
                                    3 miembros
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">Proyecto: Sistema de votación descentralizado</p>
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <span>👤</span>
                                <span>Líder: Juan Pérez</span>
                            </div>
                        </div>
                    </div>

                    <!-- Criterios de calificación -->
                    <div class="space-y-4 mb-4">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-sm font-medium text-gray-700">Innovación y Creatividad</label>
                                <span id="valor-innovacion-1" class="text-sm font-bold text-purple-600">5</span>
                            </div>
                            <input 
                                type="range" 
                                min="0" 
                                max="10" 
                                value="5" 
                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-purple-600"
                                oninput="document.getElementById('valor-innovacion-1').textContent = this.value"
                            >
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>0</span>
                                <span>10</span>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-sm font-medium text-gray-700">Funcionalidad y Calidad Técnica</label>
                                <span id="valor-funcionalidad-1" class="text-sm font-bold text-purple-600">5</span>
                            </div>
                            <input 
                                type="range" 
                                min="0" 
                                max="10" 
                                value="5" 
                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-purple-600"
                                oninput="document.getElementById('valor-funcionalidad-1').textContent = this.value"
                            >
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>0</span>
                                <span>10</span>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-sm font-medium text-gray-700">Diseño y Experiencia de Usuario</label>
                                <span id="valor-diseno-1" class="text-sm font-bold text-purple-600">5</span>
                            </div>
                            <input 
                                type="range" 
                                min="0" 
                                max="10" 
                                value="5" 
                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-purple-600"
                                oninput="document.getElementById('valor-diseno-1').textContent = this.value"
                            >
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>0</span>
                                <span>10</span>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-sm font-medium text-gray-700">Presentación y Documentación</label>
                                <span id="valor-presentacion-1" class="text-sm font-bold text-purple-600">5</span>
                            </div>
                            <input 
                                type="range" 
                                min="0" 
                                max="10" 
                                value="5" 
                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-purple-600"
                                oninput="document.getElementById('valor-presentacion-1').textContent = this.value"
                            >
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>0</span>
                                <span>10</span>
                            </div>
                        </div>
                    </div>

                    <!-- Comentarios -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Comentarios y Observaciones</label>
                        <textarea 
                            placeholder="Escribe tus comentarios sobre este equipo..."
                            rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                        ></textarea>
                    </div>

                    <!-- Calificación total -->
                    <div class="bg-purple-50 rounded-lg p-4 mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Calificación Total:</span>
                            <span class="text-2xl font-bold text-purple-600">20 / 40 pts</span>
                        </div>
                    </div>

                    <button class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-lg transition font-medium">
                        Guardar Calificación
                    </button>
                </div>

                <!-- Equipo 2 -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-xl font-bold text-gray-900">Equipo 2</h3>
                                <span class="bg-blue-100 text-blue-600 text-xs px-3 py-1 rounded-full">
                                    4 miembros
                                </span>
                            </div>
                            <p class="text-sm text-gray-600 mb-3">Proyecto: Marketplace NFT</p>
                            <div class="flex items-center gap-2 text-sm text-gray-500">
                                <span>👤</span>
                                <span>Líder: María García</span>
                            </div>
                        </div>
                    </div>

                    <!-- Criterios de calificación (similares al Equipo 1) -->
                    <div class="space-y-4 mb-4">
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-sm font-medium text-gray-700">Innovación y Creatividad</label>
                                <span id="valor-innovacion-2" class="text-sm font-bold text-purple-600">5</span>
                            </div>
                            <input 
                                type="range" 
                                min="0" 
                                max="10" 
                                value="5" 
                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-purple-600"
                                oninput="document.getElementById('valor-innovacion-2').textContent = this.value"
                            >
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>0</span>
                                <span>10</span>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-sm font-medium text-gray-700">Funcionalidad y Calidad Técnica</label>
                                <span id="valor-funcionalidad-2" class="text-sm font-bold text-purple-600">5</span>
                            </div>
                            <input 
                                type="range" 
                                min="0" 
                                max="10" 
                                value="5" 
                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-purple-600"
                                oninput="document.getElementById('valor-funcionalidad-2').textContent = this.value"
                            >
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>0</span>
                                <span>10</span>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-sm font-medium text-gray-700">Diseño y Experiencia de Usuario</label>
                                <span id="valor-diseno-2" class="text-sm font-bold text-purple-600">5</span>
                            </div>
                            <input 
                                type="range" 
                                min="0" 
                                max="10" 
                                value="5" 
                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-purple-600"
                                oninput="document.getElementById('valor-diseno-2').textContent = this.value"
                            >
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>0</span>
                                <span>10</span>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <label class="text-sm font-medium text-gray-700">Presentación y Documentación</label>
                                <span id="valor-presentacion-2" class="text-sm font-bold text-purple-600">5</span>
                            </div>
                            <input 
                                type="range" 
                                min="0" 
                                max="10" 
                                value="5" 
                                class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-purple-600"
                                oninput="document.getElementById('valor-presentacion-2').textContent = this.value"
                            >
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>0</span>
                                <span>10</span>
                            </div>
                        </div>
                    </div>

                    <!-- Comentarios -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Comentarios y Observaciones</label>
                        <textarea 
                            placeholder="Escribe tus comentarios sobre este equipo..."
                            rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent text-sm"
                        ></textarea>
                    </div>

                    <!-- Calificación total -->
                    <div class="bg-purple-50 rounded-lg p-4 mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-700">Calificación Total:</span>
                            <span class="text-2xl font-bold text-purple-600">20 / 40 pts</span>
                        </div>
                    </div>

                    <button class="w-full bg-purple-600 hover:bg-purple-700 text-white py-3 rounded-lg transition font-medium">
                        Guardar Calificación
                    </button>
                </div>
            </div>
        </div>

        <!-- Mensaje cuando no hay evento seleccionado -->
        <div id="noEventoMensaje" class="bg-white border-2 border-gray-200 rounded-2xl p-16">
            <div class="flex flex-col items-center justify-center text-center max-w-2xl mx-auto">
                <div class="mb-8">
                    <svg class="w-32 h-32 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3">Selecciona un evento para comenzar</h3>
                <p class="text-gray-600">Elige un evento activo del menú desplegable para calificar a los equipos participantes</p>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex gap-8 text-sm text-gray-600">
                    <a href="#" class="hover:text-gray-900">Acerca de</a>
                    <a href="#" class="hover:text-gray-900">Acerca de</a>
                    <a href="#" class="hover:text-gray-900">Acerca de</a>
                </div>
                <div class="flex items-center gap-2 text-purple-600">
                    <div class="text-xl">&lt;/&gt;</div>
                    <div>
                        <p class="font-bold">DevTeams</p>
                        <p class="text-xs">✨ Plataforma de Desarrollo</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function mostrarEquipos(eventoId) {
            const equiposContainer = document.getElementById('equiposContainer');
            const noEventoMensaje = document.getElementById('noEventoMensaje');
            
            if (eventoId) {
                equiposContainer.classList.remove('hidden');
                noEventoMensaje.classList.add('hidden');
            } else {
                equiposContainer.classList.add('hidden');
                noEventoMensaje.classList.remove('hidden');
            }
        }
    </script>
</body>
</html>