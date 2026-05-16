<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-blue-700">Manifest</p>
                <h1 class="text-2xl font-semibold text-slate-950">{{ $manifest->manifest_no }}</h1>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('manifests.index') }}" class="md-btn-secondary" wire:navigate>
                    <span class="material-symbols-outlined">arrow_back</span>
                    Manifests
                </a>
                <button type="button" class="md-btn-primary" onclick="window.print()">
                    <span class="material-symbols-outlined">print</span>
                    Print Manifest
                </button>
            </div>
        </div>
    </x-slot>

    @push('styles')
        <style>
            @media print {
                nav,
                header {
                    display: none !important;
                }

                body {
                    background: #fff !important;
                }
            }
        </style>
    @endpush

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="md-card">
                <div class="grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <p class="text-sm text-slate-500">Origin</p>
                        <p class="font-semibold text-slate-950">{{ $manifest->originBranch?->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Destination</p>
                        <p class="font-semibold text-slate-950">{{ $manifest->destinationBranch?->name ?? 'Direct/Mixed' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Driver</p>
                        <p class="font-semibold text-slate-950">{{ $manifest->driver_name ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Vehicle</p>
                        <p class="font-semibold text-slate-950">{{ $manifest->vehicle_number ?? '-' }}</p>
                    </div>
                </div>
            </section>

            <section class="md-card overflow-hidden">
                <div class="md-card-header flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-950">Loaded Packages</h2>
                    <span class="md-chip bg-blue-100 text-blue-800">{{ $manifest->packages->count() }} items</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                            <tr>
                                <th class="px-5 py-3">AWB</th>
                                <th class="px-5 py-3">Recipient</th>
                                <th class="px-5 py-3">Destination</th>
                                <th class="px-5 py-3">Weight</th>
                                <th class="px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach ($manifest->packages as $package)
                                <tr>
                                    <td class="px-5 py-4 font-mono font-semibold text-slate-950">{{ $package->awb }}</td>
                                    <td class="px-5 py-4 text-slate-700">{{ $package->recipient_name }}</td>
                                    <td class="px-5 py-4 text-slate-700">{{ $package->destination_city }}</td>
                                    <td class="px-5 py-4 text-slate-700">{{ number_format((float) $package->billable_weight_kg, 2) }} kg</td>
                                    <td class="px-5 py-4">
                                        <span class="md-chip bg-blue-100 text-blue-800">{{ Str::headline($package->status) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
