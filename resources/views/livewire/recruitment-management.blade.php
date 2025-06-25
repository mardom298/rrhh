<div>
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Sistema de Reclutamiento</h2>
        <p class="mt-1 text-gray-600">Gestiona ofertas laborales y procesos de selección</p>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex space-x-8">
            <button wire:click="setActiveTab('postings')"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'postings' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Ofertas Laborales
            </button>
            <button wire:click="setActiveTab('applications')"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'applications' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Aplicaciones
            </button>
            <button wire:click="setActiveTab('pipeline')"
                    class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'pipeline' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Pipeline de Candidatos
            </button>
        </nav>
    </div>

    <!-- Filtros -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                    <input type="text" wire:model.live="search" 
                           placeholder="Título, nombre o email..."
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                    <select wire:model.live="statusFilter" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos los estados</option>
                        @if($activeTab === 'postings')
                            <option value="draft">Borrador</option>
                            <option value="published">Publicado</option>
                            <option value="paused">Pausado</option>
                            <option value="closed">Cerrado</option>
                        @else
                            <option value="applied">Aplicado</option>
                            <option value="screening">Revisión</option>
                            <option value="interview">Entrevista</option>
                            <option value="test">Prueba</option>
                            <option value="offer">Oferta</option>
                            <option value="hired">Contratado</option>
                            <option value="rejected">Rechazado</option>
                        @endif
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                    <select wire:model.live="departmentFilter" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos los departamentos</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                @if($activeTab === 'postings')
                <div class="flex items-end">
                    <button wire:click="openModal" 
                            class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Nueva Oferta
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    @if($activeTab === 'postings')
        <!-- Ofertas Laborales -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Oferta
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Departamento
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aplicaciones
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha Límite
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($jobPostings as $posting)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $posting->title }}</div>
                                <div class="text-sm text-gray-500">{{ $posting->position->title ?? 'Sin puesto' }}</div>
                                <div class="text-sm text-gray-500">{{ $posting->location }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $posting->department->name ?? 'Sin departamento' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $posting->applications_count }}</div>
                            <div class="text-sm text-gray-500">{{ $posting->vacancies }} vacante(s)</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($posting->status === 'draft') bg-gray-100 text-gray-800
                                @elseif($posting->status === 'published') bg-green-100 text-green-800
                                @elseif($posting->status === 'paused') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $posting->status_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $posting->application_deadline->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            @if($posting->status === 'draft')
                                <button wire:click="publishPosting({{ $posting->id }})" 
                                        class="text-green-600 hover:text-green-900 mr-3">
                                    Publicar
                                </button>
                            @elseif($posting->status === 'published')
                                <button wire:click="pausePosting({{ $posting->id }})" 
                                        class="text-yellow-600 hover:text-yellow-900 mr-3">
                                    Pausar
                                </button>
                            @elseif($posting->status === 'paused')
                                <button wire:click="publishPosting({{ $posting->id }})" 
                                        class="text-green-600 hover:text-green-900 mr-3">
                                    Reactivar
                                </button>
                            @endif
                            <button wire:click="openModal({{ $posting->id }})" 
                                    class="text-blue-600 hover:text-blue-900 mr-3">
                                Editar
                            </button>
                            @if($posting->status !== 'closed')
                                <button wire:click="closePosting({{ $posting->id }})" 
                                        class="text-red-600 hover:text-red-900">
                                    Cerrar
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No hay ofertas laborales registradas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $jobPostings->links() }}
            </div>
        </div>

    @elseif($activeTab === 'applications')
        <!-- Aplicaciones -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Candidato
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Oferta
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Puntuación
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Fecha Aplicación
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($applications as $application)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                        <span class="text-white font-medium text-sm">
                                            {{ substr($application->first_name, 0, 1) }}{{ substr($application->last_name, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $application->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $application->email }}</div>
                                    <div class="text-sm text-gray-500">{{ $application->phone }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $application->jobPosting->title }}</div>
                            <div class="text-sm text-gray-500">{{ $application->jobPosting->department->name ?? 'Sin departamento' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $application->status_color }}">
                                {{ $application->status_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($application->score)
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-900">{{ $application->score }}/100</span>
                                    <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $application->score }}%"></div>
                                    </div>
                                </div>
                            @else
                                <span class="text-sm text-gray-500">Sin calificar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $application->applied_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="viewApplication({{ $application->id }})" 
                                    class="text-blue-600 hover:text-blue-900 mr-3">
                                Ver Detalle
                            </button>
                            @if($application->status === 'applied')
                                <button wire:click="updateApplicationStatus({{ $application->id }}, 'screening')" 
                                        class="text-green-600 hover:text-green-900 mr-3">
                                    Revisar
                                </button>
                            @endif
                            <button wire:click="updateApplicationStatus({{ $application->id }}, 'rejected')" 
                                    class="text-red-600 hover:text-red-900">
                                Rechazar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            No hay aplicaciones registradas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $applications->links() }}
            </div>
        </div>
    @endif

    <!-- Modal para crear/editar oferta -->
    @if($showModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    {{ $editingPosting ? 'Editar Oferta Laboral' : 'Nueva Oferta Laboral' }}
                </h3>
                
                <form wire:submit="save">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Título de la Oferta</label>
                            <input type="text" wire:model="title" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('title') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                            <select wire:model="department_id" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar departamento</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                            @error('department_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Puesto</label>
                            <select wire:model="position_id" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar puesto</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}">{{ $position->title }}</option>
                                @endforeach
                            </select>
                            @error('position_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Empleo</label>
                            <select wire:model="employment_type" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="full_time">Tiempo Completo</option>
                                <option value="part_time">Medio Tiempo</option>
                                <option value="contract">Contrato</option>
                                <option value="internship">Prácticas</option>
                            </select>
                            @error('employment_type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nivel de Experiencia</label>
                            <select wire:model="experience_level" 
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="entry">Sin Experiencia</option>
                                <option value="junior">Junior (1-2 años)</option>
                                <option value="mid">Semi Senior (3-5 años)</option>
                                <option value="senior">Senior (5+ años)</option>
                                <option value="executive">Ejecutivo</option>
                            </select>
                            @error('experience_level') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ubicación</label>
                            <input type="text" wire:model="location" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('location') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Número de Vacantes</label>
                            <input type="number" wire:model="vacancies" min="1"
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('vacancies') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Salario Mínimo (S/)</label>
                            <input type="number" step="0.01" wire:model="min_salary" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Salario Máximo (S/)</label>
                            <input type="number" step="0.01" wire:model="max_salary" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Límite</label>
                            <input type="date" wire:model="application_deadline" 
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('application_deadline') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" wire:model="remote_allowed" 
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200">
                            <label class="ml-2 text-sm text-gray-700">Trabajo Remoto Permitido</label>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                            <textarea wire:model="description" rows="4"
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Requisitos -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Requisitos</label>
                            @foreach($requirements as $index => $requirement)
                            <div class="flex items-center mb-2">
                                <input type="text" wire:model="requirements.{{ $index }}" 
                                       placeholder="Requisito {{ $index + 1 }}"
                                       class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <button type="button" wire:click="removeRequirement({{ $index }})" 
                                        class="ml-2 text-red-600 hover:text-red-900">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                            @endforeach
                            <button type="button" wire:click="addRequirement" 
                                    class="text-blue-600 hover:text-blue-900 text-sm">
                                + Agregar Requisito
                            </button>
                        </div>

                        <!-- Responsabilidades -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Responsabilidades</label>
                            @foreach($responsibilities as $index => $responsibility)
                            <div class="flex items-center mb-2">
                                <input type="text" wire:model="responsibilities.{{ $index }}" 
                                       placeholder="Responsabilidad {{ $index + 1 }}"
                                       class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <button type="button" wire:click="removeResponsibility({{ $index }})" 
                                        class="ml-2 text-red-600 hover:text-red-900">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                            @endforeach
                            <button type="button" wire:click="addResponsibility" 
                                    class="text-blue-600 hover:text-blue-900 text-sm">
                                + Agregar Responsabilidad
                            </button>
                        </div>

                        <!-- Beneficios -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Beneficios</label>
                            @foreach($benefits as $index => $benefit)
                            <div class="flex items-center mb-2">
                                <input type="text" wire:model="benefits.{{ $index }}" 
                                       placeholder="Beneficio {{ $index + 1 }}"
                                       class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <button type="button" wire:click="removeBenefit({{ $index }})" 
                                        class="ml-2 text-red-600 hover:text-red-900">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                            @endforeach
                            <button type="button" wire:click="addBenefit" 
                                    class="text-blue-600 hover:text-blue-900 text-sm">
                                + Agregar Beneficio
                            </button>
                        </div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" wire:click="closeModal" 
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                            Cancelar
                        </button>
                        <button type="submit" 
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                            {{ $editingPosting ? 'Actualizar' : 'Crear' }} Oferta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal para ver detalle de aplicación -->
    @if($selectedApplication)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-3/4 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        Detalle de Aplicación - {{ $selectedApplication->full_name }}
                    </h3>
                    <button wire:click="closeApplicationView" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-3">Información Personal</h4>
                        <div class="space-y-2">
                            <p><span class="font-medium">Nombre:</span> {{ $selectedApplication->full_name }}</p>
                            <p><span class="font-medium">Email:</span> {{ $selectedApplication->email }}</p>
                            <p><span class="font-medium">Teléfono:</span> {{ $selectedApplication->phone }}</p>
                            <p><span class="font-medium">DNI:</span> {{ $selectedApplication->dni }}</p>
                            <p><span class="font-medium">Dirección:</span> {{ $selectedApplication->address }}</p>
                            <p><span class="font-medium">Salario Esperado:</span> S/ {{ number_format($selectedApplication->expected_salary, 2) }}</p>
                            <p><span class="font-medium">Fecha de Aplicación:</span> {{ $selectedApplication->applied_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                    
                    <div>
                        <h4 class="text-md font-medium text-gray-900 mb-3">Oferta Laboral</h4>
                        <div class="space-y-2">
                            <p><span class="font-medium">Título:</span> {{ $selectedApplication->jobPosting->title }}</p>
                            <p><span class="font-medium">Departamento:</span> {{ $selectedApplication->jobPosting->department->name }}</p>
                            <p><span class="font-medium">Ubicación:</span> {{ $selectedApplication->jobPosting->location }}</p>
                            <p><span class="font-medium">Tipo:</span> {{ $selectedApplication->jobPosting->employment_type_name }}</p>
                        </div>
                    </div>
                </div>

                @if($selectedApplication->cover_letter)
                <div class="mt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-3">Carta de Presentación</h4>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <p class="text-sm text-gray-700">{{ $selectedApplication->cover_letter }}</p>
                    </div>
                </div>
                @endif

                @if($selectedApplication->recruitmentProcesses->count() > 0)
                <div class="mt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-3">Proceso de Selección</h4>
                    <div class="space-y-3">
                        @foreach($selectedApplication->recruitmentProcesses as $process)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                            <div>
                                <span class="font-medium">{{ $process->stage_name }}</span>
                                <span class="text-sm text-gray-500 ml-2">{{ $process->stage_date->format('d/m/Y') }}</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                @if($process->score)
                                    <span class="text-sm font-medium">{{ $process->score }}/100</span>
                                @endif
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    @if($process->result === 'pass') bg-green-100 text-green-800
                                    @elseif($process->result === 'fail') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ $process->result_name }}
                                </span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <div class="flex justify-between items-center mt-6">
                    <div class="flex space-x-3">
                        @if($selectedApplication->status !== 'hired' && $selectedApplication->status !== 'rejected')
                        <button wire:click="updateApplicationStatus({{ $selectedApplication->id }}, 'interview')" 
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                            Programar Entrevista
                        </button>
                        <button wire:click="updateApplicationStatus({{ $selectedApplication->id }}, 'test')" 
                                class="px-4 py-2 text-sm font-medium text-white bg-purple-600 rounded-md hover:bg-purple-700">
                            Enviar Prueba
                        </button>
                        <button wire:click="updateApplicationStatus({{ $selectedApplication->id }}, 'offer')" 
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                            Hacer Oferta
                        </button>
                        @endif
                    </div>
                    <button wire:click="closeApplicationView" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if (session()->has('message'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-md shadow-lg z-50">
            {{ session('message') }}
        </div>
    @endif
</div>
