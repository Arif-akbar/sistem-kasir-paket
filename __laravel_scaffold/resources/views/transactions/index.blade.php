<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-blue-700">Sales Ledger</p>
                <h1 class="text-2xl font-semibold text-slate-950">Transactions</h1>
            </div>
            <a href="{{ route('transactions.create') }}" class="md-btn-primary" wire:navigate>
                <span class="material-symbols-outlined">point_of_sale</span>
                New Transaction
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
                                <th class="px-5 py-3">Transaction</th>
                                <th class="px-5 py-3">AWB</th>
                                <th class="px-5 py-3">Route</th>
                                <th class="px-5 py-3">Cashier</th>
                                <th class="px-5 py-3 text-right">Total</th>
                                <th class="px-5 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse ($transactions as $transaction)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-4">
                                        <a href="{{ route('transactions.show', $transaction) }}" class="font-semibold text-blue-700 hover:text-blue-900" wire:navigate>
                                            {{ $transaction->transaction_no }}
                                        </a>
                                        <p class="text-xs text-slate-500">{{ $transaction->paid_at?->format('d M Y H:i') }}</p>
                                    </td>
                                    <td class="px-5 py-4 font-mono font-medium text-slate-900">{{ $transaction->package?->awb }}</td>
                                    <td class="px-5 py-4">
                                        <p class="font-medium text-slate-900">{{ $transaction->package?->originBranch?->city }} → {{ $transaction->package?->destination_city }}</p>
                                        <p class="text-xs text-slate-500">{{ Str::headline($transaction->package?->service_type ?? 'regular') }}</p>
                                    </td>
                                    <td class="px-5 py-4 text-slate-700">{{ $transaction->cashier?->name }}</td>
                                    <td class="px-5 py-4 text-right font-semibold text-slate-950">Rp {{ number_format((float) $transaction->total_amount, 0, ',', '.') }}</td>
                                    <td class="px-5 py-4">
                                        <span class="md-chip bg-green-100 text-green-800">{{ Str::headline($transaction->payment_status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-10 text-center text-slate-500">No transactions yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="mt-6">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
