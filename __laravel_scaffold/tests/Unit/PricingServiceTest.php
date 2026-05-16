<?php

namespace Tests\Unit;

use App\Services\PricingService;
use PHPUnit\Framework\TestCase;

class PricingServiceTest extends TestCase
{
    public function test_it_calculates_volumetric_and_billable_weight(): void
    {
        $pricing = new PricingService();

        $this->assertSame(10.0, $pricing->volumetricWeight(60, 40, 25));
        $this->assertSame(10.0, $pricing->billableWeight(3, 10));
        $this->assertSame(1.5, $pricing->billableWeight(1.2, 0.4));
    }

    public function test_quote_includes_tax_insurance_and_sla(): void
    {
        $quote = (new PricingService())->quote([
            'service_type' => 'express',
            'zone_code' => 'JAVA',
            'actual_weight_kg' => 2,
            'length_cm' => 40,
            'width_cm' => 30,
            'height_cm' => 20,
            'declared_value' => 1000000,
        ]);

        $this->assertSame(4.0, $quote['volumetric_weight_kg']);
        $this->assertSame(4.0, $quote['billable_weight_kg']);
        $this->assertSame(24, $quote['sla_hours']);
        $this->assertGreaterThan($quote['subtotal'], $quote['total_amount']);
    }
}
