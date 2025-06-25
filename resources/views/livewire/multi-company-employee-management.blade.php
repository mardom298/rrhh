<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Gestión Multi-Empresa</h2>
                <p class="mt-1 text-sm text-gray-600">Administra empleados que trabajan en múltiples empresas del grupo</p>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white p-4 rounded-lg shadow mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
                <input type="text" wire:model.live="search" 
                       placeholder="Nombre, DNI, ID Global..."
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Empresa</label>
                <select wire:model.live="selectedCompany" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Todas las empresas</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="filterMultiCompany" 
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Solo multi-empresa</span>
                </label>
            </div>
        </div>
    </div>

    <!-- Tabla de Empleados -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empleado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Empresa Principal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salario Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($employees as $employee)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="h-10 w-10 flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                    <span class="text-white font-medium text-sm">
                                        {{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $employee->full_name }}</div>
                                <div class="text-sm text-gray-500">
                                    ID: {{ $employee->global_employee_id }} | DNI: {{ $employee->dni }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">
                            {{ $employee->activeEmployeeCompanies->count() }} empresa(s)
                        </div>
                        <div class="text-sm text-gray-500">
                            @foreach($employee->activeEmployeeCompanies->take(2) as $ec)
                                <div class="flex items-center">
                                    <span>{{ $ec->company->name }}</span>
                                    @if($ec->is_primary_company)
                                        <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Principal</span>
                                    @endif
                                </div>
                            @endforeach
                            @if($employee->activeEmployeeCompanies->count() > 2)
                                <div class="text-xs text-gray-400">
                                    +{{ $employee->activeEmployeeCompanies->count() - 2 }} más
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ $employee->primary_company_name }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">
                            S/ {{ number_format($employee->total_salary, 2) }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button wire:click="viewEmployee({{ $employee->id }})" 
                                class="text-blue-600 hover:text-blue-900 mr-3">Ver Detalle</button>
                        <button wire:click="addCompanyToEmployee({{ $employee->id }})" 
                                class="text-green-600 hover:text-green-900">Agregar Empresa</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        No se encontraron empleados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="px-6 py-3 border-t border-gray-200">
            {{ $employees->links() }}
        </div>
    </div>

    <!-- Modal Detalle Empleado -->
    @if($showModal && $selectedEmployee)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    Detalle: {{ $selectedEmployee->full_name }}
                </h3>
                <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="space-y-4">
                <!-- Información General -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-2">Información General</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">ID Global:</span>
                            <span class="ml-2 font-medium">{{ $selectedEmployee->global_employee_id }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">DNI:</span>
                            <span class="ml-2 font-medium">{{ $selectedEmployee->dni }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Email:</span>
                            <span class="ml-2 font-medium">{{ $selectedEmployee->email }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Teléfono:</span>
                            <span class="ml-2 font-medium">{{ $selectedEmployee->phone }}</span>
                        </div>
                    </div>
                </div>

                <!-- Empresas -->
                <div>
                    <h4 class="font-medium text-gray-900 mb-3">Empresas ({{ $selectedEmployee->employeeCompanies->count() }})</h4>
                    <div class="space-y-3">
                        @foreach($selectedEmployee->employeeCompanies as $ec)
                        <div class="border rounded-lg p-4 {{ $ec->is_primary_company ? 'border-blue-300 bg-blue-50' : 'border-gray-200' }}">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <h5 class="font-medium text-gray-900">{{ $ec->company->name }}</h5>
                                        @if($ec->is_primary_company)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Principal</span>
                                        @endif
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                            {{ $ec->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $ec->status_name }}
                                        </span>
                                    </div>
                                    <div class="mt-2 grid grid-cols-2 gap-4 text-sm text-gray-600">
                                        <div>Código: {{ $ec->employee_code }}</div>
                                        <div>Contrato: {{ $ec->contract_type_name }}</div>
                                        <div>Departamento: {{ $ec->department->name ?? 'N/A' }}</div>
                                        <div>Puesto: {{ $ec->position->title ?? 'N/A' }}</div>
                                        <div>Fecha Ingreso: {{ $ec->hire_date->format('d/m/Y') }}</div>
                                        <div>Salario: S/ {{ number_format($ec->total_salary, 2) }}</div>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    @if(!$ec->is_primary_company && $ec->status === 'active')
                                        <button wire:click="setPrimaryCompany({{ $ec->id }})" 
                                                class="text-blue-600 hover:text-blue-900 text-xs">
                                            Hacer Principal
                                        </button>
                                    @endif
                                    @if($ec->status === 'active')
                                        <button wire:click="removeEmployeeFromCompany({{ $ec->id }})" 
                                                class="text-red-600 hover:text-red-900 text-xs">
                                            Remover
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal Agregar Empresa -->
    @if($showAddCompanyModal && $selectedEmployee)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">
                    Agregar {{ $selectedEmployee->full_name }} a Empresa
                </h3>
                <button wire:click="closeAddCompanyModal" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <form wire:submit="saveEmployeeCompany">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Empresa *</label>
                            <select wire:model.live="newCompanyId" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar empresa</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                            @error('newCompanyId') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Código Empleado *</label>
                            <input type="text" wire:model="newEmployeeCode" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('newEmployeeCode') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Departamento</label>
                            <select wire:model.live="newDepartmentId"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar departamento</option>
                                @foreach($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Puesto</label>
                            <select wire:model="newPositionId"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar puesto</option>
                                @foreach($positions as $position)
                                    <option value="{{ $position->id }}">{{ $position->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Salario Base *</label>
                            <input type="number" step="0.01" wire:model="newBaseSalary" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('newBaseSalary') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo Contrato *</label>
                            <select wire:model="newContractType" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="indefinido">Indefinido</option>
                                <option value="plazo_fijo">Plazo Fijo</option>
                                <option value="part_time">Medio Tiempo</option>
                                <option value="practicas">Prácticas</option>
                                <option value="consultor">Consultor</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha Ingreso *</label>
                            <input type="date" wire:model="newHireDate" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('newHireDate') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="flex items-end">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="isPrimaryCompany"
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Empresa Principal</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" wire:click="closeAddCompanyModal"
                            class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                        Agregar a Empresa
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Mensajes Flash -->
    @if (session()->has('message'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif
</div>
