<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscripción - {{ $evento->Nombre }}</title>
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

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Botón volver -->
        <a href="{{ route('eventos.show', $evento->Id) }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 mb-6">
            <span>←</span>
            <span>Volver al evento</span>
        </a>

        <!-- Encabezado -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-2xl p-8 mb-8 text-white">
            <h1 class="text-3xl font-bold mb-2">Inscripción al Evento</h1>
            <p class="text-xl">{{ $evento->Nombre }}</p>
        </div>

        @if (session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-600">❌ {{ session('error') }}</p>
            </div>
        @endif

        <!-- Formulario de inscripción -->
        <form method="POST" action="{{ route('eventos.inscribirse', $evento->Id) }}" class="bg-white rounded-2xl border border-gray-200 p-8 space-y-6">
            @csrf

            <!-- Seleccionar equipo -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Selecciona tu equipo *
                </label>
                <select name="equipo_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">-- Selecciona un equipo --</option>
                    @foreach($equipos as $equipo)
                        <option value="{{ $equipo->Id }}">
                            {{ $equipo->nombre }} ({{ $equipo->participantes->count() }} miembros)
                        </option>
                    @endforeach
                </select>
                <p class="text-sm text-gray-500 mt-1">Elige con qué equipo participarás en este evento</p>
            </div>

            <!-- Nombre del proyecto -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre del proyecto *
                </label>
                <input 
                    type="text" 
                    name="nombre_proyecto"
                    placeholder="Ej: Sistema de gestión inteligente"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    value="{{ old('nombre_proyecto') }}"
                >
                <p class="text-sm text-gray-500 mt-1">El nombre de tu proyecto para este evento</p>
            </div>

            <!-- Categoría (opcional) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Categoría (opcional)
                </label>
                <input 
                    type="text" 
                    name="categoria"
                    placeholder="Ej: Desarrollo Web, IA, IoT"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    value="{{ old('categoria') }}"
                >
            </div>

            <hr class="my-6">

            <!-- Datos del asesor -->
            <div class="bg-blue-50 rounded-lg p-6 space-y-4">
                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <span>👨‍🏫</span>
                    <span>Datos del Asesor</span>
                </h3>
                <p class="text-sm text-gray-600">Proporciona los datos de tu asesor académico</p>

                <!-- Nombre del asesor -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre completo del asesor *
                    </label>
                    <input 
                        type="text" 
                        name="asesor_nombre"
                        placeholder="Ej: Dr. Juan Pérez García"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        value="{{ old('asesor_nombre') }}"
                    >
                </div>

                <!-- Correo del asesor -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Correo electrónico del asesor *
                    </label>
                    <input 
                        type="email" 
                        name="asesor_correo"
                        placeholder="asesor@ejemplo.com"
                        required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        value="{{ old('asesor_correo') }}"
                    >
                </div>

                <!-- Teléfono del asesor -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Teléfono del asesor (opcional)
                    </label>
                    <input 
                        type="tel" 
                        name="asesor_telefono"
                        placeholder="(000) 000-0000"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        value="{{ old('asesor_telefono') }}"
                    >
                </div>
            </div>

            <!-- Botones -->
            <div class="flex gap-4 pt-4">
                <a href="{{ route('eventos.show', $evento->Id) }}" 
                   class="flex-1 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-center">
                    Cancelar
                </a>
                <button 
                    type="submit"
                    class="flex-1 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition">
                    Inscribirse al evento
                </button>
            </div>
        </form>
    </div>
</body>
</html>