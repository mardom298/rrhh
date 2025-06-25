<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PerformanceEvaluation;
use App\Models\User;
use Carbon\Carbon;

class PerformanceEvaluations extends Component
{
    use WithPagination;

    public $showModal = false;
    public $editingEvaluation = null;
    public $viewMode = 'list'; // list, create, evaluate

    // Propiedades de la evaluación
    public $user_id = '';
    public $evaluator_id = '';
    public $period = '';
    public $type = 'supervisor';
    public $evaluation_date = '';
    public $due_date = '';
    public $strengths = '';
    public $areas_for_improvement = '';
    public $goals = '';
    public $development_plan = '';
    public $evaluator_comments = '';
    public $employee_comments = '';

    // Competencias y puntuaciones
    public $competencies = [
        'comunicacion' => 0,
        'trabajo_equipo' => 0,
        'liderazgo' => 0,
        'iniciativa' => 0,
        'calidad_trabajo' => 0,
        'puntualidad' => 0,
        'adaptabilidad' => 0,
        'conocimiento_tecnico' => 0
    ];

    protected $rules = [
        'user_id' => 'required|exists:users,id',
        'evaluator_id' => 'required|exists:users,id',
        'period' => 'required|string',
        'type' => 'required|in:self,supervisor,peer,360,customer',
        'evaluation_date' => 'required|date',
        'due_date' => 'required|date|after:evaluation_date'
    ];

    public function render()
    {
        $evaluations = PerformanceEvaluation::with(['user', 'evaluator'])
            ->whereHas('user', function($query) {
                $query->where('company_id', auth()->user()->company_id);
            })
            ->orderBy('evaluation_date', 'desc')
            ->paginate(10);

        $employees = User::where('company_id', auth()->user()->company_id)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        $evaluators = User::where('company_id', auth()->user()->company_id)
            ->where('status', 'active')
            ->orderBy('first_name')
            ->get();

        return view('livewire.performance-evaluations', compact(
            'evaluations', 
            'employees', 
            'evaluators'
        ));
    }

    public function createEvaluation()
    {
        $this->resetForm();
        $this->viewMode = 'create';
        $this->evaluation_date = now()->format('Y-m-d');
        $this->due_date = now()->addWeeks(2)->format('Y-m-d');
        $this->period = now()->format('Y-Q\Q');
    }

    public function startEvaluation($evaluationId)
    {
        $this->editingEvaluation = PerformanceEvaluation::find($evaluationId);
        $this->fillForm();
        $this->viewMode = 'evaluate';
    }

    public function saveEvaluation()
    {
        $this->validate();

        $data = [
            'user_id' => $this->user_id,
            'evaluator_id' => $this->evaluator_id,
            'period' => $this->period,
            'type' => $this->type,
            'evaluation_date' => $this->evaluation_date,
            'due_date' => $this->due_date,
            'scores' => $this->competencies,
            'strengths' => $this->strengths,
            'areas_for_improvement' => $this->areas_for_improvement,
            'goals' => $this->goals,
            'development_plan' => $this->development_plan,
            'evaluator_comments' => $this->evaluator_comments,
            'employee_comments' => $this->employee_comments,
            'status' => 'draft'
        ];

        if ($this->editingEvaluation) {
            $this->editingEvaluation->update($data);
            $this->editingEvaluation->calculateOverallScore();
            session()->flash('message', 'Evaluación actualizada exitosamente.');
        } else {
            $evaluation = PerformanceEvaluation::create($data);
            $evaluation->calculateOverallScore();
            session()->flash('message', 'Evaluación creada exitosamente.');
        }

        $this->viewMode = 'list';
        $this->resetForm();
    }

    public function submitEvaluation()
    {
        if ($this->editingEvaluation) {
            $this->editingEvaluation->update([
                'status' => 'submitted',
                'submitted_at' => now()
            ]);
            session()->flash('message', 'Evaluación enviada exitosamente.');
            $this->viewMode = 'list';
        }
    }

    public function backToList()
    {
        $this->viewMode = 'list';
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->user_id = '';
        $this->evaluator_id = '';
        $this->period = '';
        $this->type = 'supervisor';
        $this->evaluation_date = '';
        $this->due_date = '';
        $this->strengths = '';
        $this->areas_for_improvement = '';
        $this->goals = '';
        $this->development_plan = '';
        $this->evaluator_comments = '';
        $this->employee_comments = '';
        $this->competencies = [
            'comunicacion' => 0,
            'trabajo_equipo' => 0,
            'liderazgo' => 0,
            'iniciativa' => 0,
            'calidad_trabajo' => 0,
            'puntualidad' => 0,
            'adaptabilidad' => 0,
            'conocimiento_tecnico' => 0
        ];
        $this->editingEvaluation = null;
    }

    private function fillForm()
    {
        $this->user_id = $this->editingEvaluation->user_id;
        $this->evaluator_id = $this->editingEvaluation->evaluator_id;
        $this->period = $this->editingEvaluation->period;
        $this->type = $this->editingEvaluation->type;
        $this->evaluation_date = $this->editingEvaluation->evaluation_date->format('Y-m-d');
        $this->due_date = $this->editingEvaluation->due_date->format('Y-m-d');
        $this->strengths = $this->editingEvaluation->strengths;
        $this->areas_for_improvement = $this->editingEvaluation->areas_for_improvement;
        $this->goals = $this->editingEvaluation->goals;
        $this->development_plan = $this->editingEvaluation->development_plan;
        $this->evaluator_comments = $this->editingEvaluation->evaluator_comments;
        $this->employee_comments = $this->editingEvaluation->employee_comments;
        
        if ($this->editingEvaluation->scores) {
            $this->competencies = array_merge($this->competencies, $this->editingEvaluation->scores);
        }
    }
}
