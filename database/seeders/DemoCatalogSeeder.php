<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DemoCatalogSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local', 'testing')) {
            throw new RuntimeException(
                'DemoCatalogSeeder hanya boleh dijalankan di local atau testing.'
            );
        }

        DB::transaction(function (): void {
            $stores = [
                [
                    'owner_name' => 'Owner Demo Wisnu',
                    'owner_email' => 'owner.wisnu@example.com',
                    'name' => 'Demo Wisnu Outdoor',
                    'slug' => 'demo-wisnu-outdoor',
                    'address' => 'Alamat toko demo Wisnu',
                ],
                [
                    'owner_name' => 'Owner Demo Fais',
                    'owner_email' => 'owner.fais@example.com',
                    'name' => 'Demo Fais Outdoor',
                    'slug' => 'demo-fais-outdoor',
                    'address' => 'Alamat toko demo Fais',
                ],
            ];

            foreach ($stores as $store) {
                $owner = User::firstOrCreate(
                    ['email' => $store['owner_email']],
                    [
                        'name' => $store['owner_name'],
                        'email_verified_at' => now(),
                        'password' => Hash::make('DemoOutdoor123!'),
                    ]
                );

                $tenant = Tenant::firstOrCreate(
                    ['slug' => $store['slug']],
                    [
                        'name' => $store['name'],
                        'description' => 'Toko contoh untuk pengembangan aplikasi.',
                        'address' => $store['address'],
                        'status' => 'active',
                    ]
                );

                $tenant->staff()->firstOrCreate(
                    ['user_id' => $owner->id],
                    [
                        'role' => 'owner',
                        'is_active' => true,
                    ]
                );

                $tentCategory = $tenant->categories()->firstOrCreate(
                    ['slug' => 'tenda'],
                    ['name' => 'Tenda']
                );

                $sleepCategory = $tenant->categories()->firstOrCreate(
                    ['slug' => 'perlengkapan-tidur'],
                    ['name' => 'Perlengkapan Tidur']
                );

                $products = [
                    [
                        'category_id' => $tentCategory->id,
                        'name' => 'Tenda Kapasitas 2 Orang',
                        'slug' => 'tenda-kapasitas-2-orang',
                        'description' => 'Tenda contoh untuk dua orang.',
                        'rental_price_per_day' => 35000,
                        'replacement_cost' => 750000,
                        'deposit_fee' => 100000,
                        'is_available' => true,
                    ],
                    [
                        'category_id' => $tentCategory->id,
                        'name' => 'Tenda Kapasitas 4 Orang',
                        'slug' => 'tenda-kapasitas-4-orang',
                        'description' => 'Tenda contoh untuk empat orang.',
                        'rental_price_per_day' => 60000,
                        'replacement_cost' => 1200000,
                        'deposit_fee' => 150000,
                        'is_available' => true,
                    ],
                    [
                        'category_id' => $sleepCategory->id,
                        'name' => 'Sleeping Bag',
                        'slug' => 'sleeping-bag',
                        'description' => 'Kantong tidur contoh untuk berkemah.',
                        'rental_price_per_day' => 15000,
                        'replacement_cost' => 250000,
                        'deposit_fee' => 50000,
                        'is_available' => true,
                    ],
                ];

                foreach ($products as $product) {
                    // Sertakan produk terhapus agar slug lama tidak dibuat ulang.
                    $tenant->products()->withTrashed()->firstOrCreate(
                        ['slug' => $product['slug']],
                        [
                            'category_id' => $product['category_id'],
                            'name' => $product['name'],
                            'description' => $product['description'],
                            'rental_price_per_day' => $product['rental_price_per_day'],
                            'replacement_cost' => $product['replacement_cost'],
                            'deposit_fee' => $product['deposit_fee'],
                            'is_available' => $product['is_available'],
                        ]
                    );
                }
            }
        });
    }
}
