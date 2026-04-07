<?php

namespace App\Livewire\Admin;

use App\Models\Project;
use App\Models\Task;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Task oversight')]
class TaskAssigner extends Component
{
    public string $filterStatus = '';

    public ?int $filterProject = null;

    public function render()
    {
        $tasks = Task::with(['project', 'team', 'assignee', 'creator'])
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterProject, fn ($q) => $q->where('project_id', $this->filterProject))
            ->latest()
            ->get();

        $projects = Project::orderBy('name')->get();

        return view('livewire.admin.task-assigner', compact('tasks', 'projects'));
    }
}
