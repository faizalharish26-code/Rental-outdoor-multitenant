<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tenant;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;

class CustomerCatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User Pemilik Tenant jika belum ada
        $owner = User::firstOrCreate(
            ['email' => 'owner@rental.test'],
            [
                'name' => 'Pemilik Rental',
                'password' => bcrypt('password'),
            ]
        );

        // 2. Buat Tenant A & Tenant B
       $tenantA = Tenant::firstOrCreate(
            ['slug' => 'eiger-adventure-rental'],
            [
                'name' => 'Eiger Adventure Rental',
                'address' => 'Jl. Merdeka No. 45, Bandung',
                
               
            ]
        );

        $tenantB = Tenant::firstOrCreate(
            ['slug' => 'consina-outdoor-store'],
            [
                'name' => 'Consina Outdoor Store',
                'address' => 'Jl. Malioboro No. 12, Yogyakarta',
             
               
            ]
        );

        // 3. Buat Kategori untuk Tenant A
        $catTendaA = Category::firstOrCreate(
            ['tenant_id' => $tenantA->id, 'slug' => 'tenda-camping'],
            ['name' => 'Tenda Camping']
        );
        $catTasA = Category::firstOrCreate(
            ['tenant_id' => $tenantA->id, 'slug' => 'tas-carrier'],
            ['name' => 'Tas Carrier']
        );

        // 4. Buat Kategori untuk Tenant B
        $catTendaB = Category::firstOrCreate(
            ['tenant_id' => $tenantB->id, 'slug' => 'tenda-dome'],
            ['name' => 'Tenda Dome']
        );

        // 5. Buat Produk untuk Tenant A
        Product::firstOrCreate(
            ['tenant_id' => $tenantA->id, 'slug' => 'tenda-eiger-tent-4-person'],
            [
                'category_id' => $catTendaA->id,
                'name' => 'Tenda Eiger 4 Orang',
                'rental_price_per_day' => 75000,
            
                'is_available' => true,
                'description' => 'Tenda double layer anti badai, kapasitas 4-5 orang, kokoh di cuaca ekstrem.',
            ]
        );

        Product::firstOrCreate(
            ['tenant_id' => $tenantA->id, 'slug' => 'carrier-eiger-60l'],
            [
                'category_id' => $catTasA->id,
                'name' => 'Carrier Eiger 60 Liter',
                'rental_price_per_day' => 45000,
             
                'is_available' => true,
                'description' => 'Tas carrier nyaman dengan sirkulasi punggung yang baik untuk pendakian 3-4 hari.',
            ]
        );

        // 6. Buat Produk untuk Tenant B
        Product::firstOrCreate(
            ['tenant_id' => $tenantB->id, 'slug' => 'tenda-consina-magnum-4'],
            [
                'category_id' => $catTendaB->id,
                'name' => 'Tenda Consina Magnum 4',
                'rental_price_per_day' => 70000,
               
                'is_available' => true,
                'description' => 'Tenda ringan berkualitas tinggi dari Consina untuk petualangan outdoor.',
            ]
        );
    }
}