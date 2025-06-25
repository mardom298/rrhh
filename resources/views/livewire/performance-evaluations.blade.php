<div>
    @if($viewMode === 'list')
        <!-- Vista de Lista -->
        <div class="mb-6">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Evaluaciones de Desempeño</h2>
                    <p class="mt-1 text-gray-600">Gestiona las evaluaciones de desempeño de los empleados</p>
                </div>
                <button wire:click="createEvaluation" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Nueva Evaluación
                </button>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Empleado
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Evaluador
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Período
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipo
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Puntuación
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($evaluations as $evaluation)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                        <span class="text-white font-medium text-sm">
                                            {{ substr($evaluation->user->first_name, 0, 1) }}{{ substr($evaluation->user->last_name, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $evaluation->user->full_name }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $evaluation->user->position->title ?? 'Sin puesto' }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $evaluation->evaluator->full_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $evaluation->period }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $evaluation->type_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($evaluation->overall_score)
                                <div class="flex items-center">
                                    <span class="text-sm font-medium text-gray-900">{{ $evaluation->overall_score }}/5</span>
                                    <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($evaluation->overall_score / 5) * 100 }}%"></div>
                                    </div>
                                </div>
                            @else
                                <span class="text-sm text-gray-500">Sin calificar</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($evaluation->status === 'draft') bg-gray-100 text-gray-800
                                @elseif($evaluation->status === 'submitted') bg-yellow-100 text-yellow-800
                                @elseif($evaluation->status === 'reviewed') bg-blue-100 text-blue-800
                                @elseif($evaluation->status === 'approved') bg-green-100 text-green-800
                                @else bg-purple-100 text-purple-800 @endif">
                                {{ $evaluation->status_name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button wire:click="startEvaluation({{ $evaluation->id }})" 
                                    class="text-blue-600 hover:text-blue-900 mr-3">
                                {{ $evaluation->status === 'draft' ? 'Evaluar' : 'Ver' }}
                            </button>
                            <button class="text-red-600 hover:text-red-900">
                                Eliminar
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            No hay evaluaciones registradas
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $evaluations->links() }}
            </div>
        </div>

    @elseif($viewMode === 'create')
        <!-- Vista de Creación -->
        <div class="mb-6">
            <div class="flex items-center">
                <button wire:click="backToList" class="mr-4 text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Nueva Evaluación de Desempeño</h2>
                    <p class="mt-1 text-gray-600">Configura una nueva evaluación para un empleado</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-lg p-6">
            <form wire:submit="saveEvaluation">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Empleado a Evaluar</label>
                        <select wire:model="user_id" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccionar empleado</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}">{{ $employee->full_name }} - {{ $employee->position->title ?? 'Sin puesto' }}</option>
                            @endforeach
                        </select>
                        @error('user_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Evaluador</label>
                        <select wire:model="evaluator_id" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccionar evaluador</option>
                            @foreach($evaluators as $evaluator)
                                <option value="{{ $evaluator->id }}">{{ $evaluator->full_name }}</option>
                            @endforeach
                        </select>
                        @error('evaluator_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Período</label>
                        <input type="text" wire:model="period" placeholder="2024-Q1" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('period') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Evaluación</label>
                        <select wire:model="type" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="supervisor">Evaluación de Supervisor</option>
                            <option value="self">Autoevaluación</option>
                            <option value="peer">Evaluación de Pares</option>
                            <option value="360">Evaluación 360°</option>
                            <option value="customer">Evaluación de Cliente</option>
                        </select>
                        @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Evaluación</label>
                        <input type="date" wire:model="evaluation_date" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('evaluation_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Límite</label>
                        <input type="date" wire:model="due_date" 
                               class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('due_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" wire:click="backToList" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                        Crear Evaluación
                    </button>
                </div>
            </form>
        </div>

    @elseif($viewMode === 'evaluate')
        <!-- Vista de Evaluación -->
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <button wire:click="backToList" class="mr-4 text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </button>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Evaluación de Desempeño</h2>
                        <p class="mt-1 text-gray-600">{{ $editingEvaluation->user->full_name }} - {{ $editingEvaluation->period }}</p>
                    </div>
                </div>
                @if($editingEvaluation->status === 'draft')
                <button wire:click="submitEvaluation" 
                        class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                    Enviar Evaluación
                </button>
                @endif
            </div>
        </div>

        <div class="space-y-6">
            <!-- Competencias -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Evaluación por Competencias</h3>
                <p class="text-sm text-gray-600 mb-6">Califica cada competencia del 1 al 5, donde 1 es "Necesita mejorar" y 5 es "Excelente"</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach([
                        'comunicacion' => 'Comunicación',
                        'trabajo_equipo' => 'Trabajo en Equipo',
                        'liderazgo' => 'Liderazgo',
                        'iniciativa' => 'Iniciativa',
                        'calidad_trabajo' => 'Calidad del Trabajo',
                        'puntualidad' => 'Puntualidad',
                        'adaptabilidad' => 'Adaptabilidad',
                        'conocimiento_tecnico' => 'Conocimiento Técnico'
                    ] as $key => $label)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}</label>
                        <div class="flex space-x-2">
                            @for($i = 1; $i <= 5; $i++)
                            <button type="button" 
                                    wire:click="$set('competencies.{{ $key }}', {{ $i }})"
                                    class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-sm font-medium
                                           {{ $competencies[$key] >= $i ? 'bg-blue-600 border-blue-600 text-white' : 'border-gray-300 text-gray-500 hover:border-blue-300' }}">
                                {{ $i }}
                            </button>
                            @endfor
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Comentarios y Desarrollo -->
            <div class="bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Comentarios y Plan de Desarrollo</h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fortalezas</label>
                        <textarea wire:model="strengths" rows="3" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Describe las principales fortalezas del empleado..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Áreas de Mejora</label>
                        <textarea wire:model="areas_for_improvement" rows="3" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Identifica las áreas que necesitan desarrollo..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Objetivos para el Próximo Período</label>
                        <textarea wire:model="goals" rows="3" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Define los objetivos específicos para el siguiente período..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plan de Desarrollo</label>
                        <textarea wire:model="development_plan" rows="3" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Describe las acciones específicas para el desarrollo del empleado..."></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Comentarios del Evaluador</label>
                        <textarea wire:model="evaluator_comments" rows="3" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Comentarios adicionales del evaluador..."></textarea>
                    </div>

                    @if($editingEvaluation->type === 'self')
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Comentarios del Empleado</label>
                        <textarea wire:model="employee_comments" rows="3" 
                                  class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Comentarios y reflexiones del empleado..."></textarea>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="flex justify-end space-x-3">
                <button wire:click="backToList" 
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                    Cancelar
                </button>
                <button wire:click="saveEvaluation" 
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700">
                    Guardar Borrador
                </button>
                @if($editingEvaluation->status === 'draft')
                <button wire:click="submitEvaluation" 
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700">
                    Enviar Evaluación
                </button>
                @endif
            </div>
        </div>
    @endif

    @if (session()->has('message'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-md shadow-lg z-50">
            {{ session('message') }}
        </div>
    @endif
</div>
