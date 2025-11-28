<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>DevTeams - Dashboard</title>
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
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Encabezado -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600">Gestiona tus equipos, eventos, invitaciones y código</p>
        </div>

        <!-- Pestañas de navegación -->
        <div class="mb-8 bg-gray-100 rounded-full p-1 inline-flex gap-1">
            <a href="{{ route('dashboard') }}" 
               class="px-8 py-3 rounded-full bg-white text-gray-900 font-medium shadow-sm flex items-center gap-2">
                <span>👥</span>
                <span>Equipos</span>
            </a>
            <a href="{{ route('eventos.index') }}" 
               class="px-8 py-3 rounded-full text-gray-600 hover:bg-white flex items-center gap-2 transition">
                <span>📅</span>
                <span>Eventos</span>
            </a>
            <a href="{{ route('codigos.index') }}" 
               class="px-8 py-3 rounded-full text-gray-600 hover:bg-white flex items-center gap-2 transition">
                <span>&lt;/&gt;</span>
                <span>Códigos</span>
            </a>
            <a href="{{ route('invitaciones.index') }}" 
               class="px-8 py-3 rounded-full text-gray-600 hover:bg-white flex items-center gap-2 transition">
                <span>✉️</span>
                <span>Invitaciones</span>
            </a>
        </div>

        <!-- Sección de equipos con botón crear -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Mis equipos</h2>
                <p class="text-gray-600">Gestiona tus equipos de desarrollo con roles especializados</p>
            </div>
            <button 
                onclick="document.getElementById('modalCrearEquipo').classList.remove('hidden')"
                class="bg-gray-900 hover:bg-gray-800 text-white px-5 py-2.5 rounded-lg font-medium flex items-center gap-2"
            >
                <span>+</span>
                <span>Crear equipo</span>
            </button>
        </div>

        <!-- Mensajes -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-green-600">✅ {{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-600">❌ {{ session('error') }}</p>
            </div>
        @endif

        <!-- Grid de equipos -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($equipos as $equipo)
                <div class="bg-white rounded-2xl border border-gray-200 p-6">
                    <!-- Cantidad de miembros -->
                    <div class="flex items-center gap-2 mb-4">
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded-full">
                            {{ $equipo->cantidadMiembros }} Miembro{{ $equipo->cantidadMiembros != 1 ? 's' : '' }}
                        </span>
                    </div>

                    <!-- Nombre del equipo -->
                    <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $equipo->nombre }}</h3>

                    <!-- Perfil (desde tabla intermedia) -->
                    @if($equipo->perfil)
                        <p class="text-sm text-gray-600 mb-4">{{ $equipo->perfil->Descripcion }}</p>
                    @else
                        <p class="text-sm text-gray-600 mb-4">Equipo de desarrollo</p>
                    @endif

                    <!-- Miembros -->
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-900 mb-2">Miembros</p>
                        <div class="space-y-2">
                            @foreach($equipo->participantes as $participante)
                                <div class="flex items-center gap-2">
                                    <span class="text-gray-600">👤</span>
                                    <span class="text-sm text-gray-700">{{ $participante->Nombre }}</span>
                                    @if($loop->first)
                                        <span class="ml-auto px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full flex items-center gap-1">
                                            <span>👑</span>
                                            <span>Líder</span>
                                        </span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="space-y-2 pt-2">
                        <button 
                            onclick="openInviteModal({{ $equipo->Id }}, '{{ $equipo->Nombre }}')"
                            class="w-full py-2.5 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition font-medium">
                            Invitar
                        </button>
                        <a href="{{ route('equipos.show', $equipo->Id) }}" 
                           class="block w-full py-2.5 bg-gray-900 text-white text-center rounded-lg hover:bg-gray-800 transition font-medium">
                            Gestionar
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-white rounded-2xl border border-gray-200">
                    <div class="text-6xl mb-4">👥</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No tienes equipos</h3>
                    <p class="text-gray-600 mb-4">Crea tu primer equipo para comenzar</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Crear Equipo -->
    <div id="modalCrearEquipo" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-gray-900">Crear Nuevo Equipo</h2>
                    <button 
                        onclick="document.getElementById('modalCrearEquipo').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600 text-2xl"
                    >
                        &times;
                    </button>
                </div>
            </div>
            
            <form method="POST" action="{{ route('equipos.store') }}" class="p-6 space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del equipo *</label>
                    <input 
                        type="text" 
                        name="nombre"
                        placeholder="Ej: Los Programadores"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                </div>

                <div class="flex gap-3 pt-4">
                    <button 
                        type="button"
                        onclick="document.getElementById('modalCrearEquipo').classList.add('hidden')"
                        class="flex-1 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition"
                    >
                        Cancelar
                    </button>
                    <button 
                        type="submit"
                        class="flex-1 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition"
                    >
                        Crear Equipo
                    </button>
                </div>
            </form>
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
            
            <form id="formInvitar" method="POST" action="" class="p-6 space-y-4">
                @csrf
                <input type="hidden" id="equipo_id" name="equipo_id">
                
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
                        required
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
        // Modal crear equipo
        document.getElementById('modalCrearEquipo').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        // Modal invitar usuario
        document.getElementById('modalInvitar').addEventListener('click', function(e) {
            if (e.target === this) {
                closeInviteModal();
            }
        });

        function openInviteModal(equipoId, nombreEquipo) {
            document.getElementById('equipo_id').value = equipoId;
            document.getElementById('formInvitar').action = '/equipos/' + equipoId + '/invitar';
            document.getElementById('modalInvitar').classList.remove('hidden');
        }

        function closeInviteModal() {
            document.getElementById('modalInvitar').classList.add('hidden');
            document.getElementById('formInvitar').reset();
        }
    </script>
</body>
</html>