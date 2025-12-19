<?php

declare(strict_types=1);

namespace App\Livewire\Projects;

use App\Models\Customer;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    use AuthorizesRequests;

    public ?Project $project = null;
    public ?int $projectId = null;

    // Form fields
    public ?int $branch_id = null;
    public string $name = '';
    public string $code = '';
    public string $description = '';
    public ?int $client_id = null;
    public ?int $project_manager_id = null;
    public ?string $start_date = null;
    public ?string $end_date = null;
    public string $status = 'planning';
    public float $budget_amount = 0;
    public ?string $notes = null;

    public function mount(?int $id = null): void
    {
        $user = auth()->user();

        if ($id) {
            $this->authorize('projects.edit');
            $this->project = Project::query()
                ->forUserBranches($user)
                ->find($id);

            abort_unless($this->project, 403);

            $this->projectId = $id;
            $this->fill($this->project->only([
                'branch_id', 'name', 'code', 'description', 'client_id', 'project_manager_id',
                'start_date', 'end_date', 'status', 'budget_amount', 'notes'
            ]));
        } else {
            $this->authorize('projects.create');
            $this->branch_id = $user?->branch_id;
        }
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:projects,code,' . $this->project?->id],
            'description' => ['required', 'string'],
            'client_id' => ['nullable', 'exists:customers,id'],
            'project_manager_id' => ['nullable', 'exists:users,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', 'in:planning,active,on_hold,completed,cancelled'],
            'budget_amount' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function save(): void
    {
        $this->validate();

        if ($this->project) {
            $this->project->update($this->only([
                'branch_id', 'name', 'code', 'description', 'client_id', 'project_manager_id',
                'start_date', 'end_date', 'status', 'budget_amount', 'notes'
            ]));
            session()->flash('success', __('Project updated successfully'));
        } else {
            Project::create(array_merge(
                $this->only([
                    'branch_id', 'name', 'code', 'description', 'client_id', 'project_manager_id',
                    'start_date', 'end_date', 'status', 'budget_amount', 'notes'
                ]),
                ['created_by' => auth()->id()]
            ));
            session()->flash('success', __('Project created successfully'));
        }

        $this->redirect(route('app.projects.index'));
    }

    public function render()
    {
        $clients = Customer::orderBy('name')->get();
        $managers = User::orderBy('name')->get();

        return view('livewire.projects.form', [
            'clients' => $clients,
            'managers' => $managers,
        ]);
    }
}
