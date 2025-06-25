<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PayrollPeriod;
use App\Models\PayrollConcept;
use App\Jobs\ProcessPayrollJob;
use Carbon\Carbon;

class PayrollManagement extends Component
{
    use WithPagination;

    public $showModal = false;
    public $showConceptModal = false;
    public $editingPeriod = null;
    public $editingConcept = null;

    // Propiedades del período
    public $name = '';
    public $start_date = '';
    public $end_date = '';
    public $payment_date = '';
    public $type = 'monthly';

    // Propiedades del concepto
    public $concept_code = '';
    public $concept_name = '';
    public $concept_description = '';
    public $concept_type = 'income';
    public $calculation_type = 'fixed';
    public $concept_value = 0;
    public $concept_formula = '';
    public $taxable = true;
    public $affects_cts = true;
    public $affects_gratification = true;
    public $affects_vacation = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'payment_date' => 'required|date',
        'type' => 'required|in:monthly,gratification,cts,bonus'
    ];

    protected $conceptRules = [
        'concept_code' => 'required|string|max:20',
        'concept_name' => 'required|string|max:255',
        'concept_type' => 'required|in:income,deduction,contribution',
        'calculation_type' => 'required|in:fixed,percentage,formula',
        'concept_value' => 'required|numeric|min:0'
    ];

    public function render()
    {
        $periods = PayrollPeriod::where('company_id', auth()->user()->company_id)
            ->orderBy('start_date', 'desc')
            ->paginate(10);

        $concepts = PayrollConcept::where('company_id', auth()->user()->company_id)
            ->where('active', true)
            ->orderBy('order')
            ->get();

        return view('livewire.payroll-management', compact('periods', 'concepts'));
    }

    public function openModal($periodId = null)
    {
        $this->resetForm();
        
        if ($periodId) {
            $this->editingPeriod = PayrollPeriod::find($periodId);
            $this->fillForm();
        } else {
            // Valores por defecto para nuevo período
            $now = Carbon::now();
            $this->start_date = $now->startOfMonth()->format('Y-m-d');
            $this->end_date = $now->endOfMonth()->format('Y-m-d');
            $this->payment_date = $now->addMonth()->day(5)->format('Y-m-d');
            $this->name = $now->format('F Y');
        }
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingPeriod = null;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'company_id' => auth()->user()->company_id,
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'payment_date' => $this->payment_date,
            'type' => $this->type,
        ];

        if ($this->editingPeriod) {
            $this->editingPeriod->update($data);
            session()->flash('message', 'Período de nómina actualizado exitosamente.');
        } else {
            PayrollPeriod::create($data);
            session()->flash('message', 'Período de nómina creado exitosamente.');
        }

        $this->closeModal();
    }

    public function calculatePayroll($periodId)
    {
        $period = PayrollPeriod::find($periodId);
        
        if ($period->status !== 'draft') {
            session()->flash('error', 'Solo se pueden calcular períodos en estado borrador.');
            return;
        }

        ProcessPayrollJob::dispatch($period);
        session()->flash('message', 'El cálculo de nómina ha sido iniciado. Recibirás una notificación cuando esté listo.');
    }

    public function openConceptModal($conceptId = null)
    {
        $this->resetConceptForm();
        
        if ($conceptId) {
            $this->editingConcept = PayrollConcept::find($conceptId);
            $this->fillConceptForm();
        }
        
        $this->showConceptModal = true;
    }

    public function closeConceptModal()
    {
        $this->showConceptModal = false;
        $this->editingConcept = null;
        $this->resetConceptForm();
    }

    public function saveConcept()
    {
        $this->validate($this->conceptRules);

        $data = [
            'company_id' => auth()->user()->company_id,
            'code' => $this->concept_code,
            'name' => $this->concept_name,
            'description' => $this->concept_description,
            'type' => $this->concept_type,
            'calculation_type' => $this->calculation_type,
            'value' => $this->concept_value,
            'formula' => $this->concept_formula,
            'taxable' => $this->taxable,
            'affects_cts' => $this->affects_cts,
            'affects_gratification' => $this->affects_gratification,
            'affects_vacation' => $this->affects_vacation,
        ];

        if ($this->editingConcept) {
            $this->editingConcept->update($data);
            session()->flash('message', 'Concepto actualizado exitosamente.');
        } else {
            PayrollConcept::create($data);
            session()->flash('message', 'Concepto creado exitosamente.');
        }

        $this->closeConceptModal();
    }

    private function resetForm()
    {
        $this->name = '';
        $this->start_date = '';
        $this->end_date = '';
        $this->payment_date = '';
        $this->type = 'monthly';
    }

    private function fillForm()
    {
        $this->name = $this->editingPeriod->name;
        $this->start_date = $this->editingPeriod->start_date->format('Y-m-d');
        $this->end_date = $this->editingPeriod->end_date->format('Y-m-d');
        $this->payment_date = $this->editingPeriod->payment_date->format('Y-m-d');
        $this->type = $this->editingPeriod->type;
    }

    private function resetConceptForm()
    {
        $this->concept_code = '';
        $this->concept_name = '';
        $this->concept_description = '';
        $this->concept_type = 'income';
        $this->calculation_type = 'fixed';
        $this->concept_value = 0;
        $this->concept_formula = '';
        $this->taxable = true;
        $this->affects_cts = true;
        $this->affects_gratification = true;
        $this->affects_vacation = true;
    }

    private function fillConceptForm()
    {
        $this->concept_code = $this->editingConcept->code;
        $this->concept_name = $this->editingConcept->name;
        $this->concept_description = $this->editingConcept->description;
        $this->concept_type = $this->editingConcept->type;
        $this->calculation_type = $this->editingConcept->calculation_type;
        $this->concept_value = $this->editingConcept->value;
        $this->concept_formula = $this->editingConcept->formula;
        $this->taxable = $this->editingConcept->taxable;
        $this->affects_cts = $this->editingConcept->affects_cts;
        $this->affects_gratification = $this->editingConcept->affects_gratification;
        $this->affects_vacation = $this->editingConcept->affects_vacation;
    }
}
