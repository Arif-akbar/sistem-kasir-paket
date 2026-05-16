<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-blue-700">Cashier POS</p>
                <h1 class="text-2xl font-semibold text-slate-950">Package Transaction</h1>
            </div>
            <a href="{{ route('transactions.index') }}" class="md-btn-secondary" wire:navigate>
                <span class="material-symbols-outlined">receipt_long</span>
                Transaction Log
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <livewire:cashier.package-entry-form />
        </div>
    </div>
</x-app-layout>
