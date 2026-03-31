<?php

namespace App\Livewire\Lead;

use App\Models\ProjectEvent;
use App\Models\Team;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Team Dashboard')]
class TeamLeadDashboard extends Component
{
    public ?int $selectedTeamId = null;

    // ── Event form state ──────────────────────────────────────────────────────
    public bool   $showEventForm    = false;
    public ?int   $editingEventId   = null;
    public string $eventTitle       = '';
    public string $eventDescription = '';
    public string $eventDate        = '';
    public string $eventType        = 'update';

    public function mount(): void
    {
        $first = auth()->user()->ledTeams()->first();
        if ($first) {
            $this->selectedTeamId = $first->id;
        }
    }

    public function selectTeam(int $id): void
    {
        $this->selectedTeamId = $id;
        $this->cancelEventForm();
    }

    // ── Event CRUD ────────────────────────────────────────────────────────────

    public function openCreateEvent(): void
    {
        $this->resetEventForm();
        $this->showEventForm  = true;
        $this->editingEventId = null;
    }

    public function openEditEvent(int $id): void
    {
        $event = ProjectEvent::findOrFail($id);
        $this->authorizeEvent($event);

        $this->editingEventId   = $id;
        $this->eventTitle       = $event->title;
        $this->eventDescription = $event->description ?? '';
        $this->eventDate        = $event->event_date->toDateString();
        $this->eventType        = $event->type;
        $this->showEventForm    = true;
    }

    public function saveEvent(): void
    {
        $data = $this->validateEvent();

        $project = $this->currentProject();

        $payload = [
            'title'       => $data['eventTitle'],
            'description' => $data['eventDescription'],
            'event_date'  => $data['eventDate'],
            'type'        => $data['eventType'],
            'project_id'  => $project->id,
            'created_by'  => auth()->id(),
        ];

        if ($this->editingEventId) {
            $event = ProjectEvent::findOrFail($this->editingEventId);
            $this->authorizeEvent($event);
            $event->update($payload);
            session()->flash('event_success', 'Event updated.');
        } else {
            ProjectEvent::create($payload);
            session()->flash('event_success', 'Event added to timeline.');
        }

        $this->cancelEventForm();
    }

    public function deleteEvent(int $id): void
    {
        $event = ProjectEvent::findOrFail($id);
        $this->authorizeEvent($event);
        $event->delete();
        session()->flash('event_success', 'Event removed.');
    }

    public function cancelEventForm(): void
    {
        $this->resetEventForm();
        $this->showEventForm = false;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function validateEvent(): array
    {
        return $this->validate([
            'eventTitle'       => 'required|string|max:255',
            'eventDescription' => 'nullable|string',
            'eventDate'        => 'required|date',
            'eventType'        => 'required|in:milestone,update,deadline',
        ]);
    }

    private function resetEventForm(): void
    {
        $this->eventTitle       = '';
        $this->eventDescription = '';
        $this->eventDate        = '';
        $this->eventType        = 'update';
        $this->editingEventId   = null;
        $this->resetValidation();
    }

    /** Ensures the event belongs to a project managed by this team lead. */
    private function authorizeEvent(ProjectEvent $event): void
    {
        $projectIds = auth()->user()
            ->ledTeams()
            ->pluck('project_id');

        abort_unless($projectIds->contains($event->project_id), 403);
    }

    /** Returns the Project for the currently selected team. */
    private function currentProject()
    {
        return Team::findOrFail($this->selectedTeamId)->project;
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        $teams = auth()->user()
            ->ledTeams()
            ->with('project')
            ->get();

        $selectedTeam    = null;
        $project         = null;
        $stats           = null;
        $tasksByPriority = collect();
        $events          = collect();
        $daysRemaining   = null;
        $progressPct     = 0;

        if ($this->selectedTeamId) {
            $selectedTeam = Team::with([
                'project.events',
                'members',
                'tasks.assignee',
            ])->find($this->selectedTeamId);

            if ($selectedTeam) {
                $project = $selectedTeam->project;
                $tasks   = $selectedTeam->tasks;

                $total       = $tasks->count();
                $done        = $tasks->where('status', 'done')->count();
                $progressPct = $total > 0 ? (int) round(($done / $total) * 100) : 0;

                $stats = [
                    'total'      => $total,
                    'done'       => $done,
                    'inProgress' => $tasks->where('status', 'in_progress')->count(),
                    'pending'    => $tasks->where('status', 'pending')->count(),
                    'overdue'    => $tasks->filter(fn ($t) => $t->isExceededDeadline())->count(),
                    'members'    => $selectedTeam->members->count(),
                ];

                $tasksByPriority = $tasks->groupBy('priority');

                $events = $project->events()
                    ->orderBy('event_date')
                    ->get()
                    ->map(function ($event) {
                        $event->is_past   = $event->event_date->isPast();
                        $event->is_today  = $event->event_date->isToday();
                        $event->days_diff = (int) now()->startOfDay()
                            ->diffInDays($event->event_date, false);
                        return $event;
                    });

                $daysRemaining = (int) now()->startOfDay()
                    ->diffInDays($project->end_date, false);
            }
        }

        return view('livewire.lead.team-lead-dashboard', compact(
            'teams', 'selectedTeam', 'project', 'stats',
            'tasksByPriority', 'events', 'daysRemaining', 'progressPct',
        ));
    }
}
