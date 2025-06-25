<div>
    <!-- Header del Grupo -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $selectedGroup->name }}</h1>
                <p class="mt-2 text-gray-600">Dashboard Consolidado del Grupo Empresarial</p>
            </div>
            <div class="flex space-x-2">
                <button wire:click="setViewMode('group')" 
                        class="px-4 py-2 rounded-md {{ $viewMode === 'group' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    Vista Grupo
                </button>
                <button wire:click="setViewMode('companies')" 
                        class="px-4 py-2 rounded-md {{ $viewMode === 'companies' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    Por Empresa
                </button>
                <button wire:click="setViewMode('employees')" 
                        class="px-4 py-2 rounded-md {{ $viewMode === 'employees' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                    Empleados Multi-Empresa
                </button>
            </div>
        </div>
    </div>

    @if($viewMode === 'group')
        <!-- Métricas del Grupo -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Empresas</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $groupStats['total_companies'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Empleados</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $groupStats['total_employees'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Nómina Total</dt>
                                <dd class="text-lg font-medium text-gray-900">S/ {{ number_format($groupStats['total_payroll'], 2) }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Multi-Empresa</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $groupStats['multi_company_employees'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actividad Reciente y Empresas -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Actividad Reciente -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Actividad Reciente</h3>
                </div>
                <div class="flow-root">
                    <ul class="-my-5 divide-y divide-gray-200">
                        @forelse($recentActivity as $activity)
                        <li class="py-4 px-6">
                            <div class="flex items-center space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-{{ $activity['color'] }}-100 flex items-center justify-center">
                                        <svg class="h-4 w-4 text-{{ $activity['color'] }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($activity['icon'] === 'user-plus')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            @endif
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">{{ $activity['message'] }}</p>
                                    <p class="text-sm text-gray-500">{{ $activity['date']->diffForHumans() }}</p>
                                </div>
                            </div>
                        </li>
                        @empty
                        <li class="py-4 px-6 text-center text-gray-500">
                            No hay actividad reciente
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            <!-- Resumen por Empresa -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Empresas del Grupo</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresa</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empleados</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nómina</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($companyStats as $company)
                            <tr class="hover:bg-gray-50 cursor-pointer" wire:click="selectCompany({{ $company['id'] }})">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $company['name'] }}</div>
                                        <div class="text-sm text-gray-500">{{ $company['code'] }} - {{ $company['type'] }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $company['employees'] }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">S/ {{ number_format($company['payroll'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    @elseif($viewMode === 'employees')
        <!-- Empleados Multi-Empresa -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Empleados que Trabajan en Múltiples Empresas</h3>
                <p class="mt-1 text-sm text-gray-600">Empleados con contratos activos en más de una empresa del grupo</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empleado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Empresas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Salario Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detalles</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($multiCompanyEmployees as $employee)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                            <span class="text-white font-medium text-sm">
                                                {{ substr($employee['name'], 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $employee['name'] }}</div>
                                        <div class="text-sm text-gray-500">ID: {{ $employee['global_id'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $employee['companies_count'] }} empresas</div>
                                <div class="text-sm text-gray-500">
                                    @foreach($employee['companies'] as $company)
                                        <div class="flex items-center">
                                            <span>{{ $company['name'] }}</span>
                                            @if($company['is_primary'])
                                                <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Principal</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                S/ {{ number_format($employee['total_salary'], 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">
                                <button class="hover:text-blue-900">Ver Detalle</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                                No hay empleados trabajando en múltiples empresas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
