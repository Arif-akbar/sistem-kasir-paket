<section class="md-card" wire:poll.60s>
    <div class="md-card-header flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-lg font-semibold text-slate-950">SLA Monitor</h2>
            <p class="text-sm text-slate-500">Nearest active deadlines</p>
        </div>
        <span class="md-chip bg-slate-100 text-slate-700">{{ $packages->count() }} active</span>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200 text-sm">
            <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                <tr>
                    <th class="px-5 py-3">AWB</th>
                    <th class="px-5 py-3">Destination</th>
                    <th class="px-5 py-3">Service</th>
                    <th class="px-5 py-3">Deadline</th>
                    <th class="px-5 py-3">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 bg-white">
                @forelse ($packages as $package)
                    @php
                        $severity = $this->severity($package);
                        $chipClass = [
                            'green' => 'bg-green-100 text-green-800',
                            'yellow' => 'bg-yellow-100 text-yellow-900',
                            'red' => 'bg-red-100 text-red-800',
                            'neutral' => 'bg-slate-100 text-slate-700',
                        ][$severity];
                    @endphp
                    <tr>
                        <td class="px-5 py-4 font-mono font-semibold text-slate-950">{{ $package->awb }}</td>
                        <td class="px-5 py-4">
                            <p class="font-medium text-slate-900">{{ $package->destination_city }}</p>
                            <p class="text-xs text-slate-500">{{ $package->originBranch?->code }} → {{ $package->destinationBranch?->code ?? 'DIRECT' }}</p>
                        </td>
                        <td class="px-5 py-4 text-slate-700">{{ Str::headline($package->service_type) }}</td>
                        <td class="px-5 py-4">
                            <p class="font-medium text-slate-950">{{ $package->sla_due_at?->format('d M Y H:i') }}</p>
                            <p class="text-xs text-slate-500">{{ $this->countdown($package) }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="md-chip {{ $chipClass }}">{{ Str::headline($severity) }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-500">No active SLA items.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
