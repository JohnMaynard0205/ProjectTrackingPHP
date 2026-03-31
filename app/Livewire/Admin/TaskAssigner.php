<?php

namespace App\Livewire\Admin;

use App\Models\Project;
use App\Models\Task;
use App\Models\Team;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Tasks')]
class TaskAssigner extends Component
{
    public string $title       = '';
    public string $description = '';
    public ?int   $projectId   = null;
    public ?int   $teamId      = null;
    public ?int   $assignedTo  = null;
    public string $startDate   = '';
    public string $dueDate     = '';
    public string $status      = 'pending';
    public string $priority    = 'medium';

    public bool $showForm  = false;
    public ?int $editingId = null;

    public string $filterStatus  = '';
    public ?int   $filterProject = null;

    protected function rules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'projectId'   => 'required|exists:projects,id',
            'teamId'      => 'required|exists:teams,id',
            'assignedTo'  => 'required|exists:users,id',
            'startDate'   => 'nullable|date',
            'dueDate'     => 'required|date',
            'status'      => 'required|in:pending,in_progress,done',
            'priority'    => 'required|in:low,medium,high',
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm  = true;
        $this->editingId = null;
    }

    public function openEdit(int $id): void
    {
        $task = Task::findOrFail($id);

        $this->editingId   = $id;
        $this->title       = $task->title;
        $this->description = $task->description ?? '';
        $this->projectId   = $task->project_id;
        $this->teamId      = $task->team_id;
        $this->assignedTo  = $task->assigned_to;
        $this->startDate   = $task->start_date?->toDateString() ?? '';
        $this->dueDate     = $task->due_date->toDateString();
        $this->status      = $task->status;
        $this->priority    = $task->priority;
        $this->showForm    = true;
    }

    public function save(): void
    {
        $data = $this->validate();

        $payload = [
            'title'       => $data['title'],
            'description' => $data['description'],
            'project_id'  => $data['projectId'],
            'team_id'     => $data['teamId'],
            'assigned_to' => $data['assignedTo'],
            'start_date'  => $data['startDate'] ?: null,
            'due_date'    => $data['dueDate'],
            'status'      => $data['status'],
            'priority'    => $data['priority'],
        ];

        if ($this->editingId) {
            Task::findOrFail($this->editingId)->update($payload);
            session()->flash('success', 'Task updated successfully.');
        } else {
            Task::create(array_merge($payload, ['created_by' => auth()->id()]));
            session()->flash('success', 'Task created successfully.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete(int $id): void
    {
        Task::findOrFail($id)->delete();
        session()->flash('success', 'Task deleted.');
    }

    public function cancelForm(): void
    {
        $this->resetForm();
        $this->showForm = false;
    }

    /** Resets team and assignee when the project changes. */
    public function updatedProjectId(): void
    {
        $this->teamId     = null;
        $this->assignedTo = null;
    }

    /** Resets assignee when the team changes. */
    public function updatedTeamId(): void
    {
        $this->assignedTo = null;
    }

    private function resetForm(): void
    {
        $this->title       = '';
        $this->description = '';
        $this->projectId   = null;
        $this->teamId      = null;
        $this->assignedTo  = null;
        $this->startDate   = '';
        $this->dueDate     = '';
        $this->status      = 'pending';
        $this->priority    = 'medium';
        $this->editingId   = null;
        $this->resetValidation();
    }

    public function render()
    {
        $tasks = Task::with(['project', 'team', 'assignee'])
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterProject, fn ($q) => $q->where('project_id', $this->filterProject))
            ->latest()
            ->get();

        $projects = Project::orderBy('name')->get();

        $teamsForForm  = $this->projectId
            ? Team::where('project_id', $this->projectId)->orderBy('name')->get()
            : collect();

        $membersForForm = $this->teamId
            ? Team::findOrFail($this->teamId)->members()->orderBy('name')->get()
            : collect();

        return view('livewire.admin.task-assigner',
            compact('tasks', 'projects', 'teamsForForm', 'membersForForm'));
    }
}
