<div x-data x-init="$nextTick(() => $refs.awb?.focus())">
    <form wire:submit.prevent="processPayment" class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
        <div class="space-y-6">
            <section class="md-card">
                <div class="md-card-header flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-slate-950">Shipment Identity</h2>
                        <p class="text-sm text-slate-500">{{ now()->format('d M Y, H:i') }}</p>
                    </div>
                    <button type="button" wire:click="generateAwb" class="md-btn-secondary">
                        <span class="material-symbols-outlined">autorenew</span>
                        New AWB
                    </button>
                </div>

                <div class="grid gap-4 p-5 md:grid-cols-2 xl:grid-cols-4">
                    <div class="md:col-span-2">
                        <label for="awb" class="md-label">AWB</label>
                        <input
                            x-ref="awb"
                            id="awb"
                            type="text"
                            class="md-field font-mono text-base font-semibold"
                            wire:model.live.debounce.150ms="awb"
                            autocomplete="off"
                            inputmode="text"
                            @keydown.enter.prevent="$refs.senderName?.focus()"
                        >
                        @error('awb') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="origin_branch_id" class="md-label">Origin Branch</label>
                        <select id="origin_branch_id" class="md-field" wire:model.live="origin_branch_id">
                            <option value="">Select branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->code }} - {{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('origin_branch_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="destination_branch_id" class="md-label">Destination Branch</label>
                        <select id="destination_branch_id" class="md-field" wire:model.live="destination_branch_id">
                            <option value="">Linehaul/Direct</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->code }} - {{ $branch->name }}</option>
                            @endforeach
                        </select>
                        @error('destination_branch_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="service_type" class="md-label">Service</label>
                        <select id="service_type" class="md-field" wire:model.live="service_type">
                            @foreach ($serviceRates as $code => $rate)
                                <option value="{{ $code }}">{{ Str::headline($code) }} · {{ $rate['sla_hours'] }}h</option>
                            @endforeach
                        </select>
                        @error('service_type') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="zone_code" class="md-label">Zone</label>
                        <select id="zone_code" class="md-field" wire:model.live="zone_code">
                            @foreach ($zoneMultipliers as $code => $multiplier)
                                <option value="{{ $code }}">{{ Str::headline($code) }} · x{{ number_format($multiplier, 2) }}</option>
                            @endforeach
                        </select>
                        @error('zone_code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="destination_city" class="md-label">Destination City</label>
                        <input id="destination_city" type="text" class="md-field" wire:model.blur="destination_city" autocomplete="address-level2">
                        @error('destination_city') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="md-card">
                    <div class="md-card-header">
                        <h2 class="text-lg font-semibold text-slate-950">Sender</h2>
                    </div>
                    <div class="space-y-4 p-5">
                        <div>
                            <label for="sender_name" class="md-label">Name</label>
                            <input x-ref="senderName" id="sender_name" type="text" class="md-field" wire:model.blur="sender_name" autocomplete="name">
                            @error('sender_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="sender_phone" class="md-label">Phone</label>
                            <input id="sender_phone" type="tel" class="md-field" wire:model.blur="sender_phone" autocomplete="tel">
                            @error('sender_phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="sender_address" class="md-label">Address</label>
                            <textarea id="sender_address" rows="4" class="md-field resize-y" wire:model.blur="sender_address" autocomplete="street-address" data-autocomplete-role="origin-address"></textarea>
                            @error('sender_address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>

                <section class="md-card">
                    <div class="md-card-header">
                        <h2 class="text-lg font-semibold text-slate-950">Recipient</h2>
                    </div>
                    <div class="space-y-4 p-5">
                        <div>
                            <label for="recipient_name" class="md-label">Name</label>
                            <input id="recipient_name" type="text" class="md-field" wire:model.blur="recipient_name" autocomplete="name">
                            @error('recipient_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="recipient_phone" class="md-label">Phone</label>
                            <input id="recipient_phone" type="tel" class="md-field" wire:model.blur="recipient_phone" autocomplete="tel">
                            @error('recipient_phone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="recipient_address" class="md-label">Address</label>
                            <textarea id="recipient_address" rows="4" class="md-field resize-y" wire:model.blur="recipient_address" autocomplete="street-address" data-autocomplete-role="destination-address"></textarea>
                            @error('recipient_address') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </section>
            </div>

            <section class="md-card">
                <div class="md-card-header">
                    <h2 class="text-lg font-semibold text-slate-950">Package Details</h2>
                </div>
                <div class="grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="actual_weight_kg" class="md-label">Actual Weight (kg)</label>
                        <input id="actual_weight_kg" type="number" step="0.01" min="0" class="md-field" wire:model.live.debounce.250ms="actual_weight_kg">
                        @error('actual_weight_kg') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="length_cm" class="md-label">Length (cm)</label>
                        <input id="length_cm" type="number" step="0.01" min="0" class="md-field" wire:model.live.debounce.250ms="length_cm">
                        @error('length_cm') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="width_cm" class="md-label">Width (cm)</label>
                        <input id="width_cm" type="number" step="0.01" min="0" class="md-field" wire:model.live.debounce.250ms="width_cm">
                        @error('width_cm') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="height_cm" class="md-label">Height (cm)</label>
                        <input id="height_cm" type="number" step="0.01" min="0" class="md-field" wire:model.live.debounce.250ms="height_cm">
                        @error('height_cm') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="content_description" class="md-label">Contents</label>
                        <input id="content_description" type="text" class="md-field" wire:model.blur="content_description">
                        @error('content_description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="declared_value" class="md-label">Declared Value</label>
                        <input id="declared_value" type="number" step="1000" min="0" class="md-field" wire:model.live.debounce.250ms="declared_value">
                        @error('declared_value') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="discount" class="md-label">Discount</label>
                        <input id="discount" type="number" step="1000" min="0" class="md-field" wire:model.live.debounce.250ms="discount">
                        @error('discount') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </section>
        </div>

        <aside class="space-y-6 xl:sticky xl:top-6 xl:self-start">
            <section class="md-card">
                <div class="md-card-header">
                    <h2 class="text-lg font-semibold text-slate-950">Billing</h2>
                </div>
                <div class="space-y-4 p-5">
                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-lg bg-slate-50 p-3">
                            <p class="text-slate-500">Volumetric</p>
                            <p class="text-xl font-semibold text-slate-950">{{ number_format((float) ($quote['volumetric_weight_kg'] ?? 0), 2) }} kg</p>
                        </div>
                        <div class="rounded-lg bg-slate-50 p-3">
                            <p class="text-slate-500">Billable</p>
                            <p class="text-xl font-semibold text-slate-950">{{ number_format((float) ($quote['billable_weight_kg'] ?? 0), 2) }} kg</p>
                        </div>
                    </div>

                    <dl class="space-y-2 text-sm">
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-500">Subtotal</dt>
                            <dd class="font-medium text-slate-900">Rp {{ number_format((float) ($quote['subtotal'] ?? 0), 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-500">Insurance</dt>
                            <dd class="font-medium text-slate-900">Rp {{ number_format((float) ($quote['insurance_fee'] ?? 0), 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-500">Tax</dt>
                            <dd class="font-medium text-slate-900">Rp {{ number_format((float) ($quote['tax'] ?? 0), 0, ',', '.') }}</dd>
                        </div>
                        <div class="flex items-center justify-between">
                            <dt class="text-slate-500">Discount</dt>
                            <dd class="font-medium text-slate-900">Rp {{ number_format((float) ($quote['discount'] ?? 0), 0, ',', '.') }}</dd>
                        </div>
                    </dl>

                    <div class="rounded-lg bg-blue-50 p-4">
                        <p class="text-sm font-medium text-blue-700">Total Due</p>
                        <p class="text-3xl font-bold text-blue-950">Rp {{ number_format((float) ($quote['total_amount'] ?? 0), 0, ',', '.') }}</p>
                    </div>
                </div>
            </section>

            <section class="md-card">
                <div class="md-card-header">
                    <h2 class="text-lg font-semibold text-slate-950">Payment</h2>
                </div>
                <div class="space-y-4 p-5">
                    <div>
                        <label for="payment_method" class="md-label">Method</label>
                        <select id="payment_method" class="md-field" wire:model.live="payment_method">
                            <option value="cash">Cash</option>
                            <option value="qris">QRIS</option>
                            <option value="card">Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                        </select>
                        @error('payment_method') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    @if ($payment_method === 'cash')
                        <div>
                            <label for="amount_paid" class="md-label">Amount Paid</label>
                            <input id="amount_paid" type="number" step="1000" min="0" class="md-field" wire:model.live.debounce.250ms="amount_paid">
                            @error('amount_paid') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="rounded-lg bg-green-50 p-4">
                            <p class="text-sm font-medium text-green-700">Change</p>
                            <p class="text-2xl font-bold text-green-950">Rp {{ number_format($change_due, 0, ',', '.') }}</p>
                        </div>
                    @endif

                    <button type="submit" class="md-btn-success w-full" wire:loading.attr="disabled">
                        <span class="material-symbols-outlined">payments</span>
                        Process Payment
                    </button>
                </div>
            </section>

            <livewire:cashier.visual-inspection />
        </aside>
    </form>
</div>
