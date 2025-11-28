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
            @forelse ($eventos as $evento)
                <div class="bg-white rounded-2xl border border-gray-200 p-6 hover:shadow-md transition relative">
                    <!-- Badge de inscrito -->
                    @if ($evento->inscrito)
                        <div class="absolute top-4 right-4">
                            <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full flex items-center gap-1">
                                ✓ INSCRITO
                            </span>
                        </div>
                    @endif

                    <!-- Etiquetas -->
                    <div class="flex gap-2 mb-4">
                        <span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">
                            📅 hackathon
                        </span>
                        @if ($evento->estado === 'proximo')
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                                🔜 Próximo
                            </span>
                        @elseif ($evento->estado === 'activo')
                            <span class="px-3 py-1 bg-orange-100 text-orange-700 text-xs font-medium rounded-full">
                                🔴 Activo
                            </span>
                        @else
                            <span class="px-3 py-1 bg-gray-200 text-gray-600 text-xs font-medium rounded-full">
                                ✓ Finalizado
                            </span>
                        @endif
                    </div>

                    <!-- Título y descripción -->
                    <h3 class="text-xl font-bold text-gray-900 mb-2 pr-20">{{ $evento->Nombre }}</h3>
                    <p class="text-gray-600 text-sm mb-4">{{ Str::limit($evento->Descripcion, 100) }}</p>

                    <!-- Fechas -->
                    <div class="space-y-2 mb-4 text-sm text-gray-600">
                        <div class="flex items-center gap-2">
                            <span>📅</span>
                            <span>Inicio: {{ \Carbon\Carbon::parse($evento->Fecha_inicio)->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span>⏰</span>
                            <span>Fin: {{ \Carbon\Carbon::parse($evento->Fecha_fin)->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    <!-- Participantes -->
                    <div class="flex items-center gap-2 mb-4 text-sm text-gray-600">
                        <span>👥</span>
                        <span>{{ $evento->proyectos->count() }} equipos inscritos</span>
                    </div>

                    <!-- Botones de acción -->
                    @if ($evento->inscrito)
                        <a href="{{ route('eventos.show', $evento->Id) }}" 
                           class="block w-full py-2.5 bg-green-600 text-white text-center rounded-lg hover:bg-green-700 transition font-medium">
                            Ver mi inscripción
                        </a>
                    @elseif ($evento->estado === 'finalizado')
                        <a href="{{ route('eventos.show', $evento->Id) }}" 
                           class="block w-full py-2.5 bg-gray-300 text-gray-600 text-center rounded-lg cursor-not-allowed">
                            Ver detalles
                        </a>
                    @else
                        <a href="{{ route('eventos.inscripcion', $evento->Id) }}" 
                           class="block w-full py-2.5 bg-gray-900 text-white text-center rounded-lg hover:bg-gray-800 transition font-medium">
                            Inscribirse
                        </a>
                    @endif
                </div>
            @empty
                <div class="col-span-full text-center py-12 bg-white rounded-2xl border border-gray-200">
                    <div class="text-6xl mb-4">📅</div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No hay eventos disponibles</h3>
                    <p class="text-gray-600">Pronto habrá nuevos eventos disponibles</p>
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>