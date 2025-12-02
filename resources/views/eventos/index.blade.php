<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DevTeams - Eventos</title>
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
               class="px-8 py-3 rounded-full text-gray-600 hover:bg-white flex items-center gap-2 transition">
                <span>👥</span>
                <span>Equipos</span>
            </a>
            <a href="{{ route('eventos.index') }}" 
               class="px-8 py-3 rounded-full bg-white text-gray-900 font-medium shadow-sm flex items-center gap-2">
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

        <!-- Sección de eventos -->
        <div class="mb-6">
            <h2 class="text-xl font-bold text-gray-900">Eventos y Retos</h2>
            <p class="text-gray-600">Participa en hackathons, concursos y desafíos de programación</p>
        </div>

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

        <!-- Grid de eventos -->
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            @forelse($eventos as $evento)
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 relative">
                    
                    {{-- ✅ Badge de inscrito --}}
                    @if($evento->estaInscrito)
                        <div class="absolute top-4 right-4 z-10">
                            <span class="bg-green-500 text-white px-4 py-2 rounded-full text-sm font-semibold shadow-lg flex items-center gap-2">
                                <i class="fas fa-check-circle"></i>
                                Inscrito
                            </span>
                        </div>
                    @endif

                    {{-- Header del evento --}}
                    <div class="bg-gradient-to-r from-purple-600 to-blue-600 p-6">
                        <h3 class="text-2xl font-bold text-white mb-2">{{ $evento->Nombre }}</h3>
                        <div class="flex items-center text-purple-100 text-sm">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            {{ $evento->Fecha_inicio->format('d/m/Y') }}
                        </div>
                    </div>

                    {{-- Contenido --}}
                    <div class="p-6">
                        <p class="text-gray-600 mb-4 line-clamp-3">{{ $evento->Descripcion }}</p>

                        <div class="space-y-2 mb-4 text-sm">
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-clock w-5 text-purple-600"></i>
                                <span>Inicio: {{ $evento->Fecha_inicio->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex items-center text-gray-700">
                                <i class="fas fa-clock w-5 text-purple-600"></i>
                                <span>Fin: {{ $evento->Fecha_fin->format('d/m/Y H:i') }}</span>
                            </div>
                            @if($evento->Ubicacion)
                                <div class="flex items-center text-gray-700">
                                    <i class="fas fa-map-marker-alt w-5 text-purple-600"></i>
                                    <span>{{ $evento->Ubicacion }}</span>
                                </div>
                            @endif
                        </div>

                        {{-- ✅ Botones según estado de inscripción --}}
                        @if($evento->estaInscrito)
                            {{-- Ya está inscrito --}}
                            <div class="space-y-2">
                                <div class="bg-green-50 border-2 border-green-500 rounded-lg p-3 text-center">
                                    <i class="fas fa-check-circle text-green-600 text-xl mb-1"></i>
                                    <p class="text-green-800 font-semibold">Ya estás inscrito en este evento</p>
                                </div>
                                <a href="{{ route('eventos.show', $evento->Id) }}" 
                                   class="block w-full bg-purple-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-purple-700 transition text-center shadow-md">
                                    <i class="fas fa-eye mr-2"></i>
                                    Ver Detalles
                                </a>
                            </div>
                        @else
                            {{-- No está inscrito --}}
                            <div class="flex gap-2">
                                <a href="{{ route('eventos.show', $evento->Id) }}" 
                                   class="flex-1 bg-gradient-to-r from-purple-600 to-blue-600 text-white px-4 py-3 rounded-lg font-semibold hover:from-purple-700 hover:to-blue-700 transition text-center shadow-md">
                                    <i class="fas fa-eye mr-2"></i>
                                    Ver Detalles
                                </a>
                                <a href="{{ route('eventos.inscripcion', $evento->Id) }}" 
                                   class="flex-1 bg-green-500 text-white px-4 py-3 rounded-lg font-semibold hover:bg-green-600 transition text-center shadow-md">
                                    <i class="fas fa-user-plus mr-2"></i>
                                    Inscribirse
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-calendar-times text-6xl text-gray-400 mb-4"></i>
                    <p class="text-xl text-gray-600">No hay eventos disponibles en este momento</p>
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>