<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevTeams - {{ $equipo->Nombre }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <!-- Navegación -->
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-3">
                    <div class="text-2xl text-purple-600">&lt;/&gt;</div>
                    <span class="text-xl font-bold">DevTeams</span>
                </div>
                
                <div class="flex items-center">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            <span>📋</span>
                            <span>Cerrar sesión</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Contenido -->
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Botón volver -->
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-6">
            <span>←</span>
            <span>Volver a equipos</span>
        </a>

        <!-- Información del equipo -->
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden mb-6">
            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 p-8">
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 bg-white/20 rounded-2xl flex items-center justify-center">
                        <span class="text-4xl">👥</span>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-1">{{ $equipo->Nombre }}</h1>
                        @if($equipo->perfil)
                            <p class="text-white/90">{{ $equipo->perfil->Descripcion }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Contenido -->
            <div class="p-8">
                <!-- Estadísticas -->
                <div class="grid md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-gray-50 rounded-xl p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                                <span class="text-purple-600">👥</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Miembros</p>
                                <p class="font-semibold text-gray-900 text-xl">{{ $equipo->cantidadMiembros }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 rounded-xl p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <span class="text-blue-600">📁</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Proyectos</p>
                                <p class="font-semibold text-gray-900 text-xl">{{ $equipo->proyectos->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                <span class="text-green-600">📅</span>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Eventos activos</p>
                                <p class="font-semibold text-gray-900 text-xl">0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Miembros del equipo -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900">Miembros del equipo</h2>
                        <button 
                            onclick="openInviteModal({{ $equipo->Id }}, '{{ $equipo->Nombre }}')"
                            class="px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition text-sm font-medium">
                            + Invitar miembro
                        </button>
                    </div>
                    
                    <div class="space-y-3">
                        @foreach($equipo->participantes as $participante)
                            <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-blue-500 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-lg">{{ substr($participante->Nombre, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $participante->Nombre }}</p>
                                        <p class="text-sm text-gray-500">{{ $participante->Correo }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($loop->first)
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-medium rounded-full flex items-center gap-1">
                                            <span>👑</span>
                                            <span>Líder</span>
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded-full">
                                            Miembro
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Proyectos -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Proyectos</h2>
                    @if($equipo->proyectos->count() > 0)
                        <div class="space-y-3">
                            @foreach($equipo->proyectos as $proyecto)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-xl hover:bg-gray-50 transition">
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $proyecto->Nombre ?? 'Proyecto sin nombre' }}</p>
                                        <p class="text-sm text-gray-500">Creado: {{ $proyecto->created_at ? $proyecto->created_at->format('d/m/Y') : 'Fecha no disponible' }}</p>
                                    </div>
                                    <a href="#" class="text-purple-600 hover:text-purple-700 text-sm font-medium">
                                        Ver detalles →
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 bg-gray-50 rounded-xl">
                            <div class="text-4xl mb-3">📁</div>
                            <p class="text-gray-600">No hay proyectos aún</p>
                        </div>
                    @endif
                </div>

                <!-- Botón salir del equipo -->
                <form action="{{ route('equipos.leave', $equipo->Id) }}" 
                      method="POST" 
                      onsubmit="return confirm('¿Estás seguro de salir de este equipo?')">
                    @csrf
                    <button type="submit" 
                            class="w-full py-3 border-2 border-red-600 text-red-600 rounded-lg hover:bg-red-50 transition font-medium">
                        Salir del equipo
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Invitar a Usuario -->
    <div id="modalInvitar" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Invitar a usuario</h2>
                        <p class="text-sm text-gray-600 mt-1">Crea un equipo para colaborar en proyectos y eventos</p>
                    </div>
                    <button 
                        onclick="closeInviteModal()"
                        class="text-gray-400 hover:text-gray-600 text-2xl"
                    >
                        &times;
                    </button>
                </div>
            </div>
            
            <form id="formInvitar" method="POST" action="/equipos/{{ $equipo->Id }}/invitar" class="p-6 space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Correo electrónico:</label>
                    <input 
                        type="email" 
                        name="email"
                        placeholder="email@ejemplo.com"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rol:</label>
                    <select 
                        name="rol"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent appearance-none bg-white"
                    >
                        <option value="">Rol en el equipo:</option>
                        <option value="lider">👑 Líder</option>
                        <option value="disenador">🎨 Diseñador</option>
                        <option value="backend">⚙️ Programador Backend</option>
                        <option value="frontend">💻 Programador Frontend</option>
                    </select>
                </div>

                <button 
                    type="submit"
                    class="w-full py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition font-medium"
                >
                    Enviar invitación
                </button>
            </form>
        </div>
    </div>

    <script>
        function openInviteModal(equipoId, nombreEquipo) {
            document.getElementById('modalInvitar').classList.remove('hidden');
        }

        function closeInviteModal() {
            document.getElementById('modalInvitar').classList.add('hidden');
            document.getElementById('formInvitar').reset();
        }

        document.getElementById('modalInvitar').addEventListener('click', function(e) {
            if (e.target === this) {
                closeInviteModal();
            }
        });
    </script>
</body>
</html>