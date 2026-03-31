<?php

namespace App\Livewire\Client;

use App\Models\Project;
use App\Models\ProjectEvent;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('My Projects')]
class ClientDashboard extends Component
{
    public ?int $selectedProjectId = null;
    public int  $month;
    public int  $year;

    public function mount(): void
    {
        $this->month = now()->month;
        $this->year  = now()->year;

        // Pre-select the first available project
        $first = $this->clientProjects()->first();
        if ($first) {
            $this->selectedProjectId = $first->id;
        }
    }

    public function selectProject(int $id): void
    {
        $this->selectedProjectId = $id;
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->month = $date->month;
        $this->year  = $date->year;
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->month = $date->month;
        $this->year  = $date->year;
    }

    public function goToToday(): void
    {
        $this->month = now()->month;
        $this->year  = now()->year;
    }

    // -------------------------------------------------------------------------

    /** Projects that belong to the authenticated client. */
    private function clientProjects(): \Illuminate\Database\Eloquent\Builder
    {
        return Project::where('client_id', auth()->id())->orderBy('name');
    }

    /** Build the 6×7 calendar grid for the current month/year. */
    private function buildCalendarGrid(Collection $eventsByDay): array
    {
        $firstDay   = Carbon::create($this->year, $this->month, 1);
        $daysInMonth = $firstDay->daysInMonth;

        // Day-of-week of the 1st (0=Sun … 6=Sat)
        $startOffset = $firstDay->dayOfWeek;

        $grid = [];
        $day  = 1;

        for ($row = 0; $row < 6; $row++) {
            $week = [];
            for ($col = 0; $col < 7; $col++) {
                $cellIndex = $row * 7 + $col;

                if ($cellIndex < $startOffset || $day > $daysInMonth) {
                    $week[] = null;
                } else {
                    $week[] = [
                        'day'    => $day,
                        'date'   => Carbon::create($this->year, $this->month, $day)->toDateString(),
                        'today'  => now()->year === $this->year
                                    && now()->month === $this->month
                                    && now()->day === $day,
                        'events' => $eventsByDay->get($day, collect()),
                    ];
                    $day++;
                }
            }

            // Only add rows that have at least one real day
            if (collect($week)->filter()->isNotEmpty()) {
                $grid[] = $week;
            }
        }

        return $grid;
    }

    public function render()
    {
        $projects = $this->clientProjects()->withCount('tasks')->get();

        $selectedProject = $this->selectedProjectId
            ? Project::with(['tasks', 'events', 'teams'])->find($this->selectedProjectId)
            : null;

        // Events in the currently displayed month, grouped by day-of-month
        $eventsByDay = collect();
        $upcomingEvents = collect();

        if ($selectedProject) {
            $monthStart = Carbon::create($this->year, $this->month, 1)->startOfDay();
            $monthEnd   = $monthStart->copy()->endOfMonth();

            $monthEvents = $selectedProject->events()
                ->whereBetween('event_date', [$monthStart, $monthEnd])
                ->orderBy('event_date')
                ->get();

            $eventsByDay = $monthEvents->groupBy(fn ($e) => $e->event_date->day);

            // Upcoming events from today onward (next 5)
            $upcomingEvents = $selectedProject->events()
                ->where('event_date', '>=', now()->toDateString())
                ->orderBy('event_date')
                ->limit(5)
                ->get();
        }

        $calendarGrid = $this->buildCalendarGrid($eventsByDay);

        // Stats for selected project
        $stats = null;
        if ($selectedProject) {
            $tasks       = $selectedProject->tasks;
            $totalTasks  = $tasks->count();
            $doneTasks   = $tasks->where('status', 'done')->count();
            $pendingTasks = $tasks->whereIn('status', ['pending', 'in_progress'])->count();
            $overdueTasks = $tasks->filter(fn ($t) => $t->isExceededDeadline())->count();

            $stats = compact('totalTasks', 'doneTasks', 'pendingTasks', 'overdueTasks');
        }

        return view('livewire.client.client-dashboard', [
            'projects'        => $projects,
            'selectedProject' => $selectedProject,
            'calendarGrid'    => $calendarGrid,
            'upcomingEvents'  => $upcomingEvents,
            'stats'           => $stats,
            'monthLabel'      => Carbon::create($this->year, $this->month, 1)->format('F Y'),
        ]);
    }
}
