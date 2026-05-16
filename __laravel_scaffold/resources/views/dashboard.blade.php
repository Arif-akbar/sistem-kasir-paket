<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-blue-700">Operations Control</p>
                <h1 class="text-2xl font-semibold text-slate-950">Dashboard</h1>
            </div>
            <a href="{{ route('transactions.create') }}" class="md-btn-primary" wire:navigate>
                <span class="material-symbols-outlined">point_of_sale</span>
                Open POS
            </a>
        </div>
    </x-slot>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    @endpush

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <section class="md-card p-5">
                    <p class="text-sm font-medium text-slate-500">Today Sales</p>
                    <p class="mt-2 text-2xl font-bold text-slate-950">Rp {{ number_format((float) $metrics['today_sales'], 0, ',', '.') }}</p>
                </section>
                <section class="md-card p-5">
                    <p class="text-sm font-medium text-slate-500">Paid Transactions</p>
                    <p class="mt-2 text-2xl font-bold text-slate-950">{{ number_format($metrics['paid_transactions']) }}</p>
                </section>
                <section class="md-card p-5">
                    <p class="text-sm font-medium text-slate-500">Open Packages</p>
                    <p class="mt-2 text-2xl font-bold text-slate-950">{{ number_format($metrics['open_packages']) }}</p>
                </section>
                <section class="md-card p-5">
                    <p class="text-sm font-medium text-slate-500">SLA Breaches</p>
                    <p class="mt-2 text-2xl font-bold text-red-700">{{ number_format($metrics['sla_breaches']) }}</p>
                </section>
            </div>

            <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_380px]">
                <section class="md-card">
                    <div class="md-card-header flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-slate-950">Delivery Heatmap</h2>
                            <p class="text-sm text-slate-500">Active delivery density</p>
                        </div>
                        <span class="md-chip bg-blue-100 text-blue-800">Leaflet</span>
                    </div>
                    <div id="delivery-heatmap" class="h-[380px] rounded-b-lg"></div>
                </section>

                <section class="md-card">
                    <div class="md-card-header">
                        <h2 class="text-lg font-semibold text-slate-950">Volume Forecast</h2>
                    </div>
                    <div class="space-y-3 p-5">
                        <div class="rounded-lg bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Model</p>
                            <p class="font-semibold text-slate-950">{{ Str::upper((string) ($forecast['model'] ?? 'offline')) }}</p>
                        </div>

                        @forelse (($forecast['predictions'] ?? []) as $prediction)
                            <div class="flex items-center justify-between rounded-lg border border-slate-200 p-3">
                                <span class="text-sm font-medium text-slate-700">{{ $prediction['date'] ?? 'Next period' }}</span>
                                <span class="text-lg font-semibold text-slate-950">{{ $prediction['volume'] ?? 0 }}</span>
                            </div>
                        @empty
                            <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                                Forecast service offline.
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            <livewire:dashboard.sla-monitor />
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
        <script>
            window.deliveryHeatmapPoints = @json($heatmapPoints);

            function initDeliveryHeatmap() {
                const el = document.getElementById('delivery-heatmap');

                if (!el || !window.L || el.dataset.ready === 'true') {
                    return;
                }

                el.dataset.ready = 'true';
                const points = window.deliveryHeatmapPoints?.length
                    ? window.deliveryHeatmapPoints
                    : [[-6.2087634, 106.845599, 0.35]];
                const map = L.map(el, { scrollWheelZoom: false }).setView([points[0][0], points[0][1]], 7);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(map);

                L.heatLayer(points, {
                    radius: 32,
                    blur: 22,
                    minOpacity: 0.35,
                    gradient: {
                        0.2: '#34a853',
                        0.55: '#fbbc04',
                        0.9: '#ea4335',
                    },
                }).addTo(map);
            }

            document.addEventListener('DOMContentLoaded', initDeliveryHeatmap);
            document.addEventListener('livewire:navigated', initDeliveryHeatmap);
        </script>
    @endpush
</x-app-layout>
