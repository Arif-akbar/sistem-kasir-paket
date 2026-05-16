<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-blue-700">Dispatch</p>
                <h1 class="text-2xl font-semibold text-slate-950">Create Manifest</h1>
            </div>
            <a href="{{ route('manifests.index') }}" class="md-btn-secondary" wire:navigate>
                <span class="material-symbols-outlined">arrow_back</span>
                Manifests
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <form method="POST" action="{{ route('manifests.store') }}" class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-[360px_minmax(0,1fr)] lg:px-8">
            @csrf

            <section class="md-card self-start">
                <div class="md-card-header">
                    <h2 class="text-lg font-semibold text-slate-950">Dispatch Details</h2>
                </div>
                <div class="space-y-4 p-5">
                    <div>
                        <label for="origin_branch_id" class="md-label">Origin</label>
                        <select id="origin_branch_id" name="origin_branch_id" class="md-field" required>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected(old('origin_branch_id', auth()->user()?->branch_id) == $branch->id)>{{ $branch->code }} - {{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('origin_branch_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="destination_branch_id" class="md-label">Destination</label>
                        <select id="destination_branch_id" name="destination_branch_id" class="md-field">
                            <option value="">Direct/Mixed</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" @selected(old('destination_branch_id') == $branch->id)>{{ $branch->code }} - {{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('destination_branch_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="driver_name" class="md-label">Driver</label>
                        <input id="driver_name" name="driver_name" type="text" class="md-field" value="{{ old('driver_name') }}">
                        @error('driver_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="vehicle_number" class="md-label">Vehicle</label>
                        <input id="vehicle_number" name="vehicle_number" type="text" class="md-field uppercase" value="{{ old('vehicle_number') }}">
                        @error('vehicle_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="notes" class="md-label">Notes</label>
                        <textarea id="notes" name="notes" rows="4" class="md-field resize-y">{{ old('notes') }}</textarea>
                        @error('notes') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="md-btn-success w-full">
                        <span class="material-symbols-outlined">print</span>
                        Print Manifest
                    </button>
                </div>
            </section>

            <section class="md-card overflow-hidden">
                <div class="md-card-header">
                    <h2 class="text-lg font-semibold text-slate-950">Ready Packages</h2>
                    @error('package_ids') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                            <tr>
                                <th class="px-5 py-3"></th>
                                <th class="px-5 py-3">AWB</th>
                                <th class="px-5 py-3">Destination</th>
                                <th class="px-5 py-3">Service</th>
                                <th class="px-5 py-3">SLA</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($packages as $package)
                                <tr>
                                    <td class="px-5 py-4">
                                        <input type="checkbox" name="package_ids[]" value="{{ $package->id }}" class="rounded border-slate-300 text-blue-600 focus:ring-blue-600" @checked(in_array($package->id, old('package_ids', [])))>
                                    </td>
                                    <td class="px-5 py-4 font-mono font-semibold text-slate-950">{{ $package->awb }}</td>
                                    <td class="px-5 py-4 text-slate-700">{{ $package->destination_city }}</td>
                                    <td class="px-5 py-4 text-slate-700">{{ Str::headline($package->service_type) }}</td>
                                    <td class="px-5 py-4 text-slate-700">{{ $package->sla_due_at?->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-slate-500">No packages ready for dispatch.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </form>
    </div>
</x-app-layout>
