<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TransactionReceiptTest extends TestCase
{
    use RefreshDatabase;

    public function test_paid_transaction_uses_read_only_show_view(): void
    {
        $branch = Branch::query()->create([
            'code' => 'JKT-01',
            'name' => 'Jakarta Central Hub',
            'address_line' => 'Jl. Sudirman No. 1',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
        ]);

        $cashier = User::factory()->create([
            'role' => 'cashier',
            'branch_id' => $branch->id,
        ]);

        $package = Package::query()->create([
            'awb' => 'AWB2605160001',
            'origin_branch_id' => $branch->id,
            'sender_name' => 'Sender One',
            'sender_phone' => '0811111111',
            'sender_address' => 'Sender address',
            'recipient_name' => 'Recipient One',
            'recipient_phone' => '0822222222',
            'recipient_address' => 'Recipient address',
            'destination_city' => 'Bandung',
            'zone_code' => 'WEST_JAVA',
            'service_type' => 'regular',
            'actual_weight_kg' => 2,
            'length_cm' => 30,
            'width_cm' => 20,
            'height_cm' => 10,
            'volumetric_weight_kg' => 1,
            'billable_weight_kg' => 2,
            'declared_value' => 0,
            'status' => 'paid',
            'sla_due_at' => now()->addDay(),
        ]);

        $transaction = Transaction::query()->create([
            'transaction_no' => 'TRX-260516-0001',
            'package_id' => $package->id,
            'cashier_id' => $cashier->id,
            'branch_id' => $branch->id,
            'subtotal' => 25000,
            'insurance_fee' => 0,
            'discount' => 0,
            'tax' => 2750,
            'total_amount' => 27750,
            'amount_paid' => 30000,
            'change_due' => 2250,
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $this->actingAs($cashier)
            ->get(route('transactions.show', $transaction))
            ->assertOk()
            ->assertViewIs('transactions.show')
            ->assertSee('Print Receipt')
            ->assertSee('AWB2605160001')
            ->assertDontSee('Edit Transaction');

        $this->assertFalse(Route::has('transactions.edit'));
    }
}
