<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\JobPosting;
use App\Models\JobApplication;
use App\Models\Department;
use App\Models\Position;
use App\Models\RecruitmentProcess;

class RecruitmentManagement extends Component
{
    use WithPagination;

    public $activeTab = 'postings'; // postings, applications, pipeline
    public $showModal = false;
    public $editingPosting = null;
    public $selectedApplication = null;

    // Filtros
    public $statusFilter = '';
    public $departmentFilter = '';
    public $search = '';

    // Propiedades del job posting
    public $title = '';
    public $description = '';
    public $department_id = '';
    public $position_id = '';
    public $employment_type = 'full_time';
    public $experience_level = 'mid';
    public $location = '';
    public $remote_allowed = false;
    public $min_salary = '';
    public $max_salary = '';
    public $application_deadline = '';
    public $vacancies = 1;
    public $requirements = [];
    public $responsibilities = [];
    public $benefits = [];

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'department_id' => 'required|exists:departments,id',
        'position_id' => 'required|exists:positions,id',
        'employment_type' => 'required|in:full_time,part_time,contract,internship',
        'experience_level' => 'required|in:entry,junior,mid,senior,executive',
        'location' => 'required|string|max:255',
        'application_deadline' => 'required|date|after:today',
        'vacancies' => 'required|integer|min:1'
    ];

    public function render()
    {
        $data = [];

        if ($this->activeTab === 'postings') {
            $data['jobPostings'] = JobPosting::with(['department', 'position'])
                ->where('company_id', auth()->user()->company_id)
                ->when($this->search, function($query) {
                    $query->where('title', 'like', '%' . $this->search . '%');
                })
                ->when($this->statusFilter, function($query) {
                    $query->where('status', $this->statusFilter);
                })
                ->when($this->departmentFilter, function($query) {
                    $query->where('department_id', $this->departmentFilter);
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } elseif ($this->activeTab === 'applications') {
            $data['applications'] = JobApplication::with(['jobPosting'])
                ->whereHas('jobPosting', function($query) {
                    $query->where('company_id', auth()->user()->company_id);
                })
                ->when($this->search, function($query) {
                    $query->where(function($q) {
                        $q->where('first_name', 'like', '%' . $this->search . '%')
                          ->orWhere('last_name', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->statusFilter, function($query) {
                    $query->where('status', $this->statusFilter);
                })
                ->orderBy('applied_at', 'desc')
                ->paginate(10);
        }

        $data['departments'] = Department::where('company_id', auth()->user()->company_id)
            ->where('active', true)
            ->orderBy('name')
            ->get();

        $data['positions'] = Position::where('company_id', auth()->user()->company_id)
            ->where('active', true)
            ->orderBy('title')
            ->get();

        return view('livewire.recruitment-management', $data);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function openModal($postingId = null)
    {
        $this->resetForm();
        
        if ($postingId) {
            $this->editingPosting = JobPosting::find($postingId);
            $this->fillForm();
        } else {
            $this->application_deadline = now()->addMonth()->format('Y-m-d');
        }
        
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->editingPosting = null;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'company_id' => auth()->user()->company_id,
            'title' => $this->title,
            'description' => $this->description,
            'department_id' => $this->department_id,
            'position_id' => $this->position_id,
            'employment_type' => $this->employment_type,
            'experience_level' => $this->experience_level,
            'location' => $this->location,
            'remote_allowed' => $this->remote_allowed,
            'min_salary' => $this->min_salary ?: null,
            'max_salary' => $this->max_salary ?: null,
            'application_deadline' => $this->application_deadline,
            'vacancies' => $this->vacancies,
            'requirements' => array_filter($this->requirements),
            'responsibilities' => array_filter($this->responsibilities),
            'benefits' => array_filter($this->benefits),
        ];

        if ($this->editingPosting) {
            $this->editingPosting->update($data);
            session()->flash('message', 'Oferta laboral actualizada exitosamente.');
        } else {
            $data['status'] = 'draft';
            JobPosting::create($data);
            session()->flash('message', 'Oferta laboral creada exitosamente.');
        }

        $this->closeModal();
    }

    public function publishPosting($postingId)
    {
        $posting = JobPosting::find($postingId);
        $posting->update(['status' => 'published']);
        session()->flash('message', 'Oferta laboral publicada exitosamente.');
    }

    public function pausePosting($postingId)
    {
        $posting = JobPosting::find($postingId);
        $posting->update(['status' => 'paused']);
        session()->flash('message', 'Oferta laboral pausada.');
    }

    public function closePosting($postingId)
    {
        $posting = JobPosting::find($postingId);
        $posting->update(['status' => 'closed']);
        session()->flash('message', 'Oferta laboral cerrada.');
    }

    public function viewApplication($applicationId)
    {
        $this->selectedApplication = JobApplication::with(['jobPosting', 'recruitmentProcesses.interviewer'])
            ->find($applicationId);
    }

    public function closeApplicationView()
    {
        $this->selectedApplication = null;
    }

    public function updateApplicationStatus($applicationId, $status)
    {
        $application = JobApplication::find($applicationId);
        $application->update(['status' => $status]);

        // Crear registro en el proceso de reclutamiento
        RecruitmentProcess::create([
            'job_posting_id' => $application->job_posting_id,
            'job_application_id' => $applicationId,
            'stage' => $status,
            'stage_date' => now(),
            'interviewer_id' => auth()->id(),
            'result' => 'pending'
        ]);

        session()->flash('message', 'Estado de la aplicación actualizado.');
        $this->closeApplicationView();
    }

    public function addRequirement()
    {
        $this->requirements[] = '';
    }

    public function removeRequirement($index)
    {
        unset($this->requirements[$index]);
        $this->requirements = array_values($this->requirements);
    }

    public function addResponsibility()
    {
        $this->responsibilities[] = '';
    }

    public function removeResponsibility($index)
    {
        unset($this->responsibilities[$index]);
        $this->responsibilities = array_values($this->responsibilities);
    }

    public function addBenefit()
    {
        $this->benefits[] = '';
    }

    public function removeBenefit($index)
    {
        unset($this->benefits[$index]);
        $this->benefits = array_values($this->benefits);
    }

    private function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->department_id = '';
        $this->position_id = '';
        $this->employment_type = 'full_time';
        $this->experience_level = 'mid';
        $this->location = '';
        $this->remote_allowed = false;
        $this->min_salary = '';
        $this->max_salary = '';
        $this->application_deadline = '';
        $this->vacancies = 1;
        $this->requirements = [''];
        $this->responsibilities = [''];
        $this->benefits = [''];
    }

    private function fillForm()
    {
        $this->title = $this->editingPosting->title;
        $this->description = $this->editingPosting->description;
        $this->department_id = $this->editingPosting->department_id;
        $this->position_id = $this->editingPosting->position_id;
        $this->employment_type = $this->editingPosting->employment_type;
        $this->experience_level = $this->editingPosting->experience_level;
        $this->location = $this->editingPosting->location;
        $this->remote_allowed = $this->editingPosting->remote_allowed;
        $this->min_salary = $this->editingPosting->min_salary;
        $this->max_salary = $this->editingPosting->max_salary;
        $this->application_deadline = $this->editingPosting->application_deadline->format('Y-m-d');
        $this->vacancies = $this->editingPosting->vacancies;
        $this->requirements = $this->editingPosting->requirements ?: [''];
        $this->responsibilities = $this->editingPosting->responsibilities ?: [''];
        $this->benefits = $this->editingPosting->benefits ?: [''];
    }
}
