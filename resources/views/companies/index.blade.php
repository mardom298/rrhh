@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <h2 class="text-2xl font-bold mb-6">Gestión de Empresas</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($companies as $company)
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ $company->name }}</h3>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            {{ ucfirst($company->status) }}
                        </span>
                    </div>
                    
                    <div class="space-y-2 text-sm text-gray-600">
                        <p><span class="font-medium">RUC:</span> {{ $company->ruc }}</p>
                        <p><span class="font-medium">Razón Social:</span> {{ $company->business_name }}</p>
                        <p><span class="font-medium">Empleados:</span> {{ $company->activeEmployees->count() }}</p>
                        <p><span class="font-medium">Departamentos:</span> {{ $company->departments->count() }}</p>
                        <p><span class="font-medium">Nómina Total:</span> S/ {{ number_format($company->activeEmployees->sum('total_salary'), 2) }}</p>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-xs text-gray-500">{{ $company->description }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="mt-6">
                {{ $companies->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
