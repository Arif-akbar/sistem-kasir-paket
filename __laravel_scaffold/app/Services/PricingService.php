<?php

namespace App\Services;

class PricingService
{
    /**
     * @var array<string, array{base:int, per_kg:int, sla_hours:int}>
     */
    private array $serviceRates = [
        'regular' => ['base' => 12000, 'per_kg' => 6000, 'sla_hours' => 48],
        'express' => ['base' => 22000, 'per_kg' => 9500, 'sla_hours' => 24],
        'same_day' => ['base' => 35000, 'per_kg' => 14000, 'sla_hours' => 8],
    ];

    /**
     * @var array<string, float>
     */
    private array $zoneMultipliers = [
        'LOCAL' => 1.00,
        'WEST_JAVA' => 1.18,
        'JAVA' => 1.35,
        'NATIONAL' => 1.85,
    ];

    public function volumetricWeight(float|int|string|null $length, float|int|string|null $width, float|int|string|null $height): float
    {
        $length = max(0, (float) $length);
        $width = max(0, (float) $width);
        $height = max(0, (float) $height);

        return round(($length * $width * $height) / 6000, 2);
    }

    public function billableWeight(float|int|string|null $actualWeight, float|int|string|null $volumetricWeight): float
    {
        $weight = max(1, (float) $actualWeight, (float) $volumetricWeight);

        return ceil($weight * 2) / 2;
    }

    /**
     * @param array{
     *     service_type?: string,
     *     zone_code?: string,
     *     actual_weight_kg?: mixed,
     *     length_cm?: mixed,
     *     width_cm?: mixed,
     *     height_cm?: mixed,
     *     declared_value?: mixed,
     *     discount?: mixed
     * } $payload
     * @return array<string, float|int|string>
     */
    public function quote(array $payload): array
    {
        $serviceType = $payload['service_type'] ?? 'regular';
        $zoneCode = strtoupper($payload['zone_code'] ?? 'LOCAL');

        $rate = $this->serviceRates[$serviceType] ?? $this->serviceRates['regular'];
        $multiplier = $this->zoneMultipliers[$zoneCode] ?? $this->zoneMultipliers['NATIONAL'];

        $volumetricWeight = $this->volumetricWeight(
            $payload['length_cm'] ?? 0,
            $payload['width_cm'] ?? 0,
            $payload['height_cm'] ?? 0,
        );
        $billableWeight = $this->billableWeight($payload['actual_weight_kg'] ?? 0, $volumetricWeight);

        $subtotal = round(($rate['base'] + ($billableWeight * $rate['per_kg'])) * $multiplier, 2);
        $insuranceFee = round(max(0, (float) ($payload['declared_value'] ?? 0)) * 0.002, 2);
        $discount = round(max(0, (float) ($payload['discount'] ?? 0)), 2);
        $tax = round(max(0, ($subtotal + $insuranceFee - $discount)) * 0.11, 2);
        $total = round(max(0, $subtotal + $insuranceFee + $tax - $discount), 2);

        return [
            'service_type' => $serviceType,
            'zone_code' => $zoneCode,
            'volumetric_weight_kg' => $volumetricWeight,
            'billable_weight_kg' => $billableWeight,
            'subtotal' => $subtotal,
            'insurance_fee' => $insuranceFee,
            'discount' => $discount,
            'tax' => $tax,
            'total_amount' => $total,
            'sla_hours' => $rate['sla_hours'],
        ];
    }

    /**
     * @return array<string, array{base:int, per_kg:int, sla_hours:int}>
     */
    public function serviceRates(): array
    {
        return $this->serviceRates;
    }

    /**
     * @return array<string, float>
     */
    public function zoneMultipliers(): array
    {
        return $this->zoneMultipliers;
    }
}
