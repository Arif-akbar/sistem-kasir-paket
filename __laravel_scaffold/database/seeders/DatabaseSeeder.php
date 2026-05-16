<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $jakarta = Branch::query()->create([
            'code' => 'JKT-01',
            'name' => 'Jakarta Central Hub',
            'phone' => '+62 21 5550 1001',
            'email' => 'jakarta.hub@example.com',
            'address_line' => 'Jl. Jenderal Sudirman No. 12',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'postal_code' => '10220',
            'latitude' => -6.2087634,
            'longitude' => 106.845599,
            'operating_hours' => [
                'weekday' => '08:00-21:00',
                'weekend' => '09:00-17:00',
            ],
        ]);

        $bandung = Branch::query()->create([
            'code' => 'BDG-01',
            'name' => 'Bandung City Branch',
            'phone' => '+62 22 5550 2002',
            'email' => 'bandung.branch@example.com',
            'address_line' => 'Jl. Asia Afrika No. 8',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'postal_code' => '40111',
            'latitude' => -6.9174639,
            'longitude' => 107.6191228,
            'operating_hours' => [
                'weekday' => '08:00-20:00',
                'weekend' => '09:00-16:00',
            ],
        ]);

        User::query()->create([
            'name' => 'Admin Operations',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'branch_id' => $jakarta->id,
            'email_verified_at' => now(),
        ]);

        User::query()->create([
            'name' => 'Manager Hub',
            'email' => 'manager@example.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'branch_id' => $jakarta->id,
            'email_verified_at' => now(),
        ]);

        User::query()->create([
            'name' => 'Cashier Bandung',
            'email' => 'cashier@example.com',
            'password' => Hash::make('password'),
            'role' => 'cashier',
            'branch_id' => $bandung->id,
            'email_verified_at' => now(),
        ]);
    }
}
