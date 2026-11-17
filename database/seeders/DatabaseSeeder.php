<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        user::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@gmail.com',
            'password' => 'admin1234',
        ]);

        $this->call([
            \Database\Seeders\CustomerCatalogSeeder::class,
        ]);
    }
}
