<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-blue-700">Paid Transaction</p>
                <h1 class="text-2xl font-semibold text-slate-950">{{ $transaction->transaction_no }}</h1>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('transactions.index') }}" class="md-btn-secondary" wire:navigate>
                    <span class="material-symbols-outlined">arrow_back</span>
                    Log
                </a>
                <button type="button" class="md-btn-primary" onclick="window.print()">
                    <span class="material-symbols-outlined">print</span>
                    Print Receipt
                </button>
            </div>
        </div>
    </x-slot>

    @push('styles')
        <style>
            @media print {
                nav,
                header,
                .no-print {
                    display: none !important;
                }

                body {
                    background: #fff !important;
                }

                .receipt-paper {
                    box-shadow: none !important;
                    border: 0 !important;
                    max-width: 80mm !important;
                    margin: 0 auto !important;
                }
            }
        </style>
    @endpush

    <div class="py-8">
        <div class="mx-auto grid max-w-7xl gap-6 px-4 sm:px-6 lg:grid-cols-[420px_minmax(0,1fr)] lg:px-8">
            <section class="receipt-paper md-card p-5">
                <div class="border-b border-dashed border-slate-300 pb-4 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-lg bg-blue-600 text-white">
                        <span class="material-symbols-outlined">local_shipping</span>
                    </div>
                    <h2 class="mt-3 text-lg font-bold text-slate-950">PaketPOS</h2>
                    <p class="text-xs text-slate-500">{{ $transaction->branch?->name }}</p>
                </div>

                <div class="space-y-3 border-b border-dashed border-slate-300 py-4 text-sm">
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Receipt</span>
                        <span class="text-right font-semibold text-slate-950">{{ $transaction->transaction_no }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">AWB</span>
                        <span class="font-mono font-semibold text-slate-950">{{ $transaction->package->awb }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Paid</span>
                        <span class="text-right text-slate-950">{{ $transaction->paid_at?->format('d M Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Cashier</span>
                        <span class="text-right text-slate-950">{{ $transaction->cashier?->name }}</span>
                    </div>
                </div>

                <div class="space-y-3 border-b border-dashed border-slate-300 py-4 text-sm">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Sender</p>
                        <p class="font-medium text-slate-950">{{ $transaction->package->sender_name }}</p>
                        <p class="text-slate-600">{{ $transaction->package->sender_phone }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-slate-500">Recipient</p>
                        <p class="font-medium text-slate-950">{{ $transaction->package->recipient_name }}</p>
                        <p class="text-slate-600">{{ $transaction->package->recipient_phone }}</p>
                        <p class="text-slate-600">{{ $transaction->package->destination_city }}</p>
                    </div>
                </div>

                <dl class="space-y-2 border-b border-dashed border-slate-300 py-4 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Service</dt>
                        <dd class="font-medium text-slate-950">{{ Str::headline($transaction->package->service_type) }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Billable Weight</dt>
                        <dd class="font-medium text-slate-950">{{ number_format((float) $transaction->package->billable_weight_kg, 2) }} kg</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Subtotal</dt>
                        <dd>Rp {{ number_format((float) $transaction->subtotal, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Insurance</dt>
                        <dd>Rp {{ number_format((float) $transaction->insurance_fee, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Tax</dt>
                        <dd>Rp {{ number_format((float) $transaction->tax, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Discount</dt>
                        <dd>Rp {{ number_format((float) $transaction->discount, 0, ',', '.') }}</dd>
                    </div>
                </dl>

                <div class="space-y-2 pt-4 text-sm">
                    <div class="flex justify-between gap-4 text-lg font-bold text-slate-950">
                        <span>Total</span>
                        <span>Rp {{ number_format((float) $transaction->total_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Paid</span>
                        <span>Rp {{ number_format((float) $transaction->amount_paid, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span class="text-slate-500">Change</span>
                        <span>Rp {{ number_format((float) $transaction->change_due, 0, ',', '.') }}</span>
                    </div>
                </div>
            </section>

            <section class="no-print grid gap-6">
                <div class="md-card">
                    <div class="md-card-header">
                        <h2 class="text-lg font-semibold text-slate-950">Shipment Summary</h2>
                    </div>
                    <div class="grid gap-4 p-5 md:grid-cols-2">
                        <div class="rounded-lg bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Origin</p>
                            <p class="font-semibold text-slate-950">{{ $transaction->package->originBranch?->name }}</p>
                            <p class="text-sm text-slate-600">{{ $transaction->package->sender_address }}</p>
                        </div>
                        <div class="rounded-lg bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Destination</p>
                            <p class="font-semibold text-slate-950">{{ $transaction->package->destination_city }}</p>
                            <p class="text-sm text-slate-600">{{ $transaction->package->recipient_address }}</p>
                        </div>
                        <div class="rounded-lg bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">SLA Due</p>
                            <p class="font-semibold text-slate-950">{{ $transaction->package->sla_due_at?->format('d M Y H:i') }}</p>
                        </div>
                        <div class="rounded-lg bg-slate-50 p-4">
                            <p class="text-sm text-slate-500">Payment</p>
                            <p class="font-semibold text-slate-950">{{ Str::headline($transaction->payment_method) }} · {{ Str::headline($transaction->payment_status) }}</p>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>
