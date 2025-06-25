<div>
    <!-- Header -->
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Centro de Reportes</h2>
        <p class="mt-1 text-gray-600">Genera reportes detallados de todas las áreas de RRHH</p>
    </div>

    <!-- Configuración del Reporte -->
    <div class="bg-white shadow rounded-lg mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Configuración del Reporte</h3>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipo de Reporte</label>
                    <select wire:model.live="reportType" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="attendance">Asistencia</option>
                        <option value="payroll">Nómina</option>
                        <option value="performance">Evaluaciones de Desempeño</option>
                        <option value="recruitment">Reclutamiento</option>
                        <option value="training">Capacitación</option>
                    </select>
                </div>

                @if($reportType !== 'payroll' && $reportType !== 'performance')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                    <input type="date" wire:model="start_date" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                    <input type="date" wire:model="end_date" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                @endif

                @if($reportType === 'payroll')
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Período de Nómina</label>
                    <select wire:model="period_id" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccionar período</option>
                        @foreach($payrollPeriods as $period)
                            <option value="{{ $period->id }}">{{ $period->name }} ({{ $period->start_date->format('d/m/Y') }} - {{ $period->end_date->format('d/m/Y') }})</option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if($reportType === 'performance')
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Período de Evaluación</label>
                    <input type="text" wire:model="evaluation_period" placeholder="2024-Q1" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Departamento</label>
                    <select wire:model="department_id" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos los departamentos</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex justify-between items-center">
                <div class="flex space-x-3">
                    <button wire:click="generateReport" 
                            wire:loading.attr="disabled"
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                        <span wire:loading.remove wire:target="generateReport">Generar Reporte</span>
                        <span wire:loading wire:target="generateReport">Generando...</span>
                    </button>
                    
                    @if($reportData)
                    <button wire:click="clearReport" 
                            class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700">
                        Limpiar
                    </button>
                    @endif
                </div>

                @if($reportData)
                <div class="flex space-x-2">
                    <button wire:click="exportReport('pdf')" 
                            class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        Exportar PDF
                    </button>
                    <button wire:click="exportReport('excel')" 
                            class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                        Exportar Excel
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Resultados del Reporte -->
    @if($reportData)
    <div class="space-y-6">
        <!-- Estadísticas Generales -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    Resumen Ejecutivo
                    @if(isset($reportData['period']))
                        <span class="text-sm text-gray-500 ml-2">
                            @if(isset($reportData['period']['start_date']))
                                ({{ $reportData['period']['start_date'] }} - {{ $reportData['period']['end_date'] }})
                            @else
                                ({{ $reportData['period'] }})
                            @endif
                        </span>
                    @endif
                </h3>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach($reportData['stats'] as $key => $value)
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600">
                            @if(is_numeric($value))
                                @if(str_contains($key, 'rate') || str_contains($key, 'percentage'))
                                    {{ $value }}%
                                @elseif(str_contains($key, 'salary') || str_contains($key, 'amount'))
                                    S/ {{ number_format($value, 2) }}
                                @else
                                    {{ number_format($value) }}
                                @endif
                            @else
                                {{ $value }}
                            @endif
                        </div>
                        <div class="text-sm text-gray-500 capitalize">
                            {{ str_replace('_', ' ', $key) }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if($reportType === 'attendance')
            <!-- Reporte de Asistencia -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Datos por Empleado -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-md font-medium text-gray-900">Asistencia por Empleado</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empleado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Presente</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tardanzas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">% Asistencia</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($reportData['employee_data'] as $employee)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $employee['employee_name'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee['department'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee['present_days'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee['late_days'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-900">{{ $employee['attendance_rate'] }}%</span>
                                            <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $employee['attendance_rate'] }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Datos por Departamento -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-md font-medium text-gray-900">Asistencia por Departamento</h4>
                    </div>
                    <div class="p-6">
                        <canvas id="departmentAttendanceChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>

        @elseif($reportType === 'payroll')
            <!-- Reporte de Nómina -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Datos por Empleado -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-md font-medium text-gray-900">Nómina por Empleado</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empleado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bruto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Descuentos</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Neto</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($reportData['employee_data'] as $employee)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $employee['employee_name'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee['department'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/ {{ number_format($employee['gross_salary'], 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/ {{ number_format($employee['total_deductions'], 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">S/ {{ number_format($employee['net_salary'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Datos por Concepto -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-md font-medium text-gray-900">Conceptos de Nómina</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Concepto</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($reportData['concept_data'] as $concept)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $concept['concept'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($concept['type'] === 'income') bg-green-100 text-green-800
                                            @elseif($concept['type'] === 'deduction') bg-red-100 text-red-800
                                            @else bg-blue-100 text-blue-800 @endif">
                                            {{ ucfirst($concept['type']) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/ {{ number_format($concept['total_amount'], 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        @elseif($reportType === 'performance')
            <!-- Reporte de Evaluaciones -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Distribución de Puntuaciones -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-md font-medium text-gray-900">Distribución de Puntuaciones</h4>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($reportData['score_distribution'] as $level => $count)
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $level) }}</span>
                                <div class="flex items-center">
                                    <span class="text-sm text-gray-900 mr-2">{{ $count }}</span>
                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                        <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $reportData['stats']['total_evaluations'] > 0 ? ($count / $reportData['stats']['total_evaluations']) * 100 : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Análisis por Competencias -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-md font-medium text-gray-900">Análisis por Competencias</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Competencia</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Promedio</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Evaluaciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($reportData['competency_analysis'] as $competency)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $competency['competency']) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-900">{{ $competency['average_score'] }}/5</span>
                                            <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ ($competency['average_score'] / 5) * 100 }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $competency['evaluations_count'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        @elseif($reportType === 'recruitment')
            <!-- Reporte de Reclutamiento -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Datos por Oferta -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-md font-medium text-gray-900">Resultados por Oferta Laboral</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Oferta</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aplicaciones</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contratados</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">% Conversión</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($reportData['job_posting_data'] as $job)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $job['job_title'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $job['department'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $job['total_applications'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $job['hired'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-900">{{ $job['conversion_rate'] }}%</span>
                                            <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $job['conversion_rate'] }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Distribución por Estado -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-md font-medium text-gray-900">Estado de Aplicaciones</h4>
                    </div>
                    <div class="p-6">
                        <canvas id="recruitmentStatusChart" width="400" height="300"></canvas>
                    </div>
                </div>
            </div>

        @elseif($reportType === 'training')
            <!-- Reporte de Capacitación -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Datos por Programa -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-md font-medium text-gray-900">Programas de Capacitación</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Programa</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inscritos</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Completados</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">% Finalización</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($reportData['program_data'] as $program)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $program['program_title'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $program['category'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $program['total_enrolled'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $program['completed'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="text-sm font-medium text-gray-900">{{ $program['completion_rate'] }}%</span>
                                            <div class="ml-2 w-16 bg-gray-200 rounded-full h-2">
                                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $program['completion_rate'] }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Datos por Empleado -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h4 class="text-md font-medium text-gray-900">Top Empleados en Capacitación</h4>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empleado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Capacitaciones</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Certificaciones</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Horas</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($reportData['employee_data']->sortByDesc('completed_trainings')->take(10) as $employee)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $employee['employee_name'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee['department'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee['completed_trainings'] }}/{{ $employee['total_trainings'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee['certifications_earned'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $employee['total_hours'] }}h</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($reportData && $reportType === 'attendance' && isset($reportData['department_data']))
        // Gráfico de asistencia por departamento
        const deptCtx = document.getElementById('departmentAttendanceChart');
        if (deptCtx) {
            new Chart(deptCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: {!! json_encode($reportData['department_data']->pluck('department')) !!},
                    datasets: [{
                        label: 'Tasa de Asistencia (%)',
                        data: {!! json_encode($reportData['department_data']->pluck('attendance_rate')) !!},
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }
        @endif

        @if($reportData && $reportType === 'recruitment' && isset($reportData['status_distribution']))
        // Gráfico de estado de reclutamiento
        const recruitCtx = document.getElementById('recruitmentStatusChart');
        if (recruitCtx) {
            new Chart(recruitCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($reportData['status_distribution']->pluck('status')) !!},
                    datasets: [{
                        data: {!! json_encode($reportData['status_distribution']->pluck('count')) !!},
                        backgroundColor: [
                            '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
                            '#06B6D4', '#84CC16', '#F97316', '#EC4899', '#6B7280'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
        @endif
    });
    </script>
    @endif

    @if (session()->has('message'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-md shadow-lg z-50">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-md shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif
</div>
