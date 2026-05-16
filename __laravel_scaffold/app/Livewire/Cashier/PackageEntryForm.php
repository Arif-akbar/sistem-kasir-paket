<?php

namespace App\Livewire\Cashier;

use App\Models\Branch;
use App\Models\Package;
use App\Models\Transaction;
use App\Services\PricingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class PackageEntryForm extends Component
{
    public string $awb = '';

    public ?int $origin_branch_id = null;

    public ?int $destination_branch_id = null;

    public string $sender_name = '';

    public string $sender_phone = '';

    public string $sender_address = '';

    public string $recipient_name = '';

    public string $recipient_phone = '';

    public string $recipient_address = '';

    public string $destination_city = '';

    public string $zone_code = 'LOCAL';

    public string $service_type = 'regular';

    public string $content_description = '';

    public float|string $actual_weight_kg = 1;

    public float|string $length_cm = 0;

    public float|string $width_cm = 0;

    public float|string $height_cm = 0;

    public float|string $declared_value = 0;

    public float|string $discount = 0;

    public string $payment_method = 'cash';

    public float|string $amount_paid = 0;

    /**
     * @var array<string, float|int|string>
     */
    public array $quote = [];

    public float $change_due = 0;

    public function mount(): void
    {
        $this->origin_branch_id = auth()->user()?->branch_id ?? Branch::query()->value('id');
        $this->generateAwb();
        $this->refreshQuote();
    }

    /**
     * @return array<string, list<string>>
     */
    protected function rules(): array
    {
        return [
            'awb' => ['required', 'string', 'max:64', 'unique:packages,awb'],
            'origin_branch_id' => ['required', 'exists:branches,id'],
            'destination_branch_id' => ['nullable', 'exists:branches,id'],
            'sender_name' => ['required', 'string', 'max:255'],
            'sender_phone' => ['required', 'string', 'max:32'],
            'sender_address' => ['required', 'string', 'max:1000'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'recipient_phone' => ['required', 'string', 'max:32'],
            'recipient_address' => ['required', 'string', 'max:1000'],
            'destination_city' => ['required', 'string', 'max:255'],
            'zone_code' => ['required', 'string', 'max:16'],
            'service_type' => ['required', 'in:regular,express,same_day'],
            'content_description' => ['nullable', 'string', 'max:255'],
            'actual_weight_kg' => ['required', 'numeric', 'min:0.01', 'max:9999'],
            'length_cm' => ['required', 'numeric', 'min:0', 'max:9999'],
            'width_cm' => ['required', 'numeric', 'min:0', 'max:9999'],
            'height_cm' => ['required', 'numeric', 'min:0', 'max:9999'],
            'declared_value' => ['nullable', 'numeric', 'min:0'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,qris,card,bank_transfer'],
            'amount_paid' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function updated(string $name): void
    {
        if ($name === 'awb') {
            $this->awb = strtoupper(trim($this->awb));
        }

        if (in_array($name, [
            'actual_weight_kg',
            'length_cm',
            'width_cm',
            'height_cm',
            'declared_value',
            'discount',
            'service_type',
            'zone_code',
            'payment_method',
            'amount_paid',
        ], true)) {
            $this->refreshQuote();
        }
    }

    public function generateAwb(): void
    {
        do {
            $awb = 'AWB'.now()->format('ymdHis').Str::upper(Str::random(4));
        } while (Package::query()->where('awb', $awb)->exists());

        $this->awb = $awb;
    }

    public function refreshQuote(): void
    {
        $this->quote = app(PricingService::class)->quote($this->quotePayload());
        $paid = $this->payment_method === 'cash'
            ? (float) $this->amount_paid
            : (float) $this->quote['total_amount'];

        $this->change_due = max(0, round($paid - (float) $this->quote['total_amount'], 2));
    }

    public function processPayment(): void
    {
        $validated = $this->validate();
        $quote = app(PricingService::class)->quote($this->quotePayload());
        $amountPaid = $validated['payment_method'] === 'cash'
            ? (float) ($validated['amount_paid'] ?? 0)
            : (float) $quote['total_amount'];

        if ($amountPaid < (float) $quote['total_amount']) {
            $this->addError('amount_paid', 'Payment is less than the total due.');

            return;
        }

        $transaction = DB::transaction(function () use ($validated, $quote, $amountPaid): Transaction {
            $package = Package::query()->create([
                'awb' => strtoupper($validated['awb']),
                'origin_branch_id' => $validated['origin_branch_id'],
                'destination_branch_id' => $validated['destination_branch_id'] ?? null,
                'sender_name' => $validated['sender_name'],
                'sender_phone' => $validated['sender_phone'],
                'sender_address' => $validated['sender_address'],
                'recipient_name' => $validated['recipient_name'],
                'recipient_phone' => $validated['recipient_phone'],
                'recipient_address' => $validated['recipient_address'],
                'destination_city' => $validated['destination_city'],
                'zone_code' => $quote['zone_code'],
                'service_type' => $quote['service_type'],
                'content_description' => $validated['content_description'] ?? null,
                'actual_weight_kg' => $validated['actual_weight_kg'],
                'length_cm' => $validated['length_cm'],
                'width_cm' => $validated['width_cm'],
                'height_cm' => $validated['height_cm'],
                'volumetric_weight_kg' => $quote['volumetric_weight_kg'],
                'billable_weight_kg' => $quote['billable_weight_kg'],
                'declared_value' => $validated['declared_value'] ?? 0,
                'status' => 'paid',
                'sla_due_at' => now()->addHours((int) $quote['sla_hours']),
            ]);

            return Transaction::query()->create([
                'transaction_no' => 'TRX-'.now()->format('ymd-His').'-'.Str::upper(Str::random(4)),
                'package_id' => $package->id,
                'cashier_id' => auth()->id(),
                'branch_id' => $validated['origin_branch_id'],
                'subtotal' => $quote['subtotal'],
                'insurance_fee' => $quote['insurance_fee'],
                'discount' => $quote['discount'],
                'tax' => $quote['tax'],
                'total_amount' => $quote['total_amount'],
                'amount_paid' => $amountPaid,
                'change_due' => max(0, $amountPaid - (float) $quote['total_amount']),
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'paid',
                'paid_at' => now(),
            ]);
        });

        $this->redirectRoute('transactions.show', ['transaction' => $transaction->id], navigate: true);
    }

    public function render()
    {
        return view('livewire.cashier.package-entry-form', [
            'branches' => Branch::query()->where('is_active', true)->orderBy('name')->get(),
            'serviceRates' => app(PricingService::class)->serviceRates(),
            'zoneMultipliers' => app(PricingService::class)->zoneMultipliers(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function quotePayload(): array
    {
        return [
            'service_type' => $this->service_type,
            'zone_code' => $this->zone_code,
            'actual_weight_kg' => $this->actual_weight_kg,
            'length_cm' => $this->length_cm,
            'width_cm' => $this->width_cm,
            'height_cm' => $this->height_cm,
            'declared_value' => $this->declared_value,
            'discount' => $this->discount,
        ];
    }
}
