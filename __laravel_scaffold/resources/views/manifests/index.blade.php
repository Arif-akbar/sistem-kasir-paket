<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-blue-700">Linehaul Operations</p>
                <h1 class="text-2xl font-semibold text-slate-950">Manifests</h1>
            </div>
            <a href="{{ route('manifests.create') }}" class="md-btn-primary" wire:navigate>
                <span class="material-symbols-outlined">assignment_add</span>
                Create Manifest
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <section class="md-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">
                            <tr>
                                <th class="px-5 py-3">Manifest</th>
                                <th class="px-5 py-3">Route</th>
                                <th class="px-5 py-3">Driver</th>
                                <th class="px-5 py-3">Packages</th>
                                <th class="px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($manifests as $manifest)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-4">
                                        <a href="{{ route('manifests.show', $manifest) }}" class="font-semibold text-blue-700 hover:text-blue-900" wire:navigate>
                                            {{ $manifest->manifest_no }}
                                        </a>
                                        <p class="text-xs text-slate-500">{{ $manifest->dispatched_at?->format('d M Y H:i') ?? $manifest->created_at->format('d M Y H:i') }}</p>
                                    </td>
                                    <td class="px-5 py-4">
                                        <p class="font-medium text-slate-900">{{ $manifest->originBranch?->code }} → {{ $manifest->destinationBranch?->code ?? 'DIRECT' }}</p>
                                        <p class="text-xs text-slate-500">{{ $manifest->originBranch?->city }} to {{ $manifest->destinationBranch?->city ?? 'multiple zones' }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-slate-700">{{ $manifest->driver_name ?? '-' }}</td>
                                    <td class="px-5 py-4 font-semibold text-slate-950">{{ $manifest->packages_count }}</td>
                                    <td class="px-5 py-4">
                                        <span class="md-chip bg-blue-100 text-blue-800">{{ Str::headline($manifest->status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-slate-500">No manifests yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="mt-6">
                {{ $manifests->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
