<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Transaction;
use App\Services\PythonApiService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function __invoke(PythonApiService $pythonApi): View
    {
        $today = Carbon::today();

        $metrics = [
            'today_sales' => Transaction::query()
                ->where('payment_status', 'paid')
                ->whereDate('paid_at', $today)
                ->sum('total_amount'),
            'paid_transactions' => Transaction::query()
                ->where('payment_status', 'paid')
                ->whereDate('paid_at', $today)
                ->count(),
            'open_packages' => Package::query()
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->count(),
            'sla_breaches' => Package::query()
                ->whereNotIn('status', ['delivered', 'cancelled'])
                ->whereNotNull('sla_due_at')
                ->where('sla_due_at', '<', now())
                ->count(),
        ];

        $forecast = $pythonApi->forecastVolume([
            'branch' => auth()->user()?->branch?->code,
            'horizon_days' => 7,
        ]);

        $heatmapPoints = Package::query()
            ->with('destinationBranch')
            ->whereNotIn('status', ['delivered', 'cancelled'])
            ->limit(100)
            ->get()
            ->map(fn (Package $package): array => [
                (float) ($package->destinationBranch?->latitude ?? $package->originBranch?->latitude ?? -6.2087634),
                (float) ($package->destinationBranch?->longitude ?? $package->originBranch?->longitude ?? 106.845599),
                $package->status === 'in_transit' ? 0.7 : 0.45,
            ])
            ->values();

        return view('dashboard', [
            'metrics' => $metrics,
            'forecast' => $forecast,
            'heatmapPoints' => $heatmapPoints,
        ]);
    }
}
