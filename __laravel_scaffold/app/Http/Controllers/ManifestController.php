<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Manifest;
use App\Models\Package;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ManifestController extends Controller
{
    public function index(): View
    {
        $manifests = Manifest::query()
            ->with(['originBranch', 'destinationBranch'])
            ->withCount('packages')
            ->latest()
            ->paginate(15);

        return view('manifests.index', [
            'manifests' => $manifests,
        ]);
    }

    public function create(): View
    {
        $packages = Package::query()
            ->whereIn('status', ['paid', 'ready_for_dispatch'])
            ->latest()
            ->limit(50)
            ->get();

        return view('manifests.create', [
            'branches' => Branch::query()->where('is_active', true)->orderBy('name')->get(),
            'packages' => $packages,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'origin_branch_id' => ['required', 'exists:branches,id'],
            'destination_branch_id' => ['nullable', 'exists:branches,id'],
            'driver_name' => ['nullable', 'string', 'max:255'],
            'vehicle_number' => ['nullable', 'string', 'max:32'],
            'package_ids' => ['required', 'array', 'min:1'],
            'package_ids.*' => ['integer', 'exists:packages,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $manifest = DB::transaction(function () use ($validated): Manifest {
            $manifest = Manifest::query()->create([
                'manifest_no' => 'MF-'.now()->format('ymd-His').'-'.Str::upper(Str::random(4)),
                'origin_branch_id' => $validated['origin_branch_id'],
                'destination_branch_id' => $validated['destination_branch_id'] ?? null,
                'driver_name' => $validated['driver_name'] ?? null,
                'vehicle_number' => $validated['vehicle_number'] ?? null,
                'status' => 'dispatched',
                'dispatched_at' => now(),
                'notes' => $validated['notes'] ?? null,
            ]);

            $manifest->packages()->syncWithPivotValues($validated['package_ids'], [
                'loaded_by' => auth()->id(),
                'scanned_at' => now(),
            ]);

            Package::query()
                ->whereIn('id', $validated['package_ids'])
                ->update(['status' => 'in_transit']);

            return $manifest;
        });

        return redirect()->route('manifests.show', $manifest);
    }

    public function show(Manifest $manifest): View
    {
        $manifest->load(['originBranch', 'destinationBranch', 'packages.transaction', 'packages.originBranch']);

        return view('manifests.show', [
            'manifest' => $manifest,
        ]);
    }
}
