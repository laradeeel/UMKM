<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        Admin::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'image_path' => '/public/img',
            'phone_number' => '08123',
            'address' => 'skipp',
            'job' => 'owner',
            'username' => 'admin',
            'password' => 'admin'
        ]);
    }
}
