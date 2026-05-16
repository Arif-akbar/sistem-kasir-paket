<?php

namespace App\Livewire\Dashboard;

use App\Models\Package;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class SlaMonitor extends Component
{
    public ?int $branchId = null;

    public function mount(): void
    {
        $this->branchId = auth()->user()?->branch_id;
    }

    public function severity(Package $package): string
    {
        if (! $package->sla_due_at) {
            return 'neutral';
        }

        if ($package->sla_due_at->isPast()) {
            return 'red';
        }

        if ($package->sla_due_at->diffInMinutes(now()) <= 240) {
            return 'yellow';
        }

        return 'green';
    }

    public function countdown(Package $package): string
    {
        if (! $package->sla_due_at) {
            return 'No SLA';
        }

        if ($package->sla_due_at->isPast()) {
            return 'Overdue '.$package->sla_due_at->diffForHumans();
        }

        return $package->sla_due_at->diffForHumans(null, true).' left';
    }

    public function render()
    {
        /** @var Collection<int, Package> $packages */
        $packages = Package::query()
            ->with(['originBranch', 'destinationBranch'])
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->whereNotNull('sla_due_at')
            ->when($this->branchId, fn ($query): mixed => $query->where('origin_branch_id', $this->branchId))
            ->orderBy('sla_due_at')
            ->limit(12)
            ->get();

        return view('livewire.dashboard.sla-monitor', [
            'packages' => $packages,
        ]);
    }
}
