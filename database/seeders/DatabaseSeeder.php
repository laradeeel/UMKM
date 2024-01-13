<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Menu;

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

    Menu::create([
        "id" => 1,
                "id_admin" => 1,
                "name" => "ayambakar taliwang",
                "desc" => "Makanan khas bali",
                "image_path" => "img/menu/1/fP1CJxSJLsNWxA4ybWlspnRtrNbX1wWRzqlQpMrD.png",
                "number_available" => 44,
                "number_sale" => 0,
                "favorite" => 0,
                "food_type" => "Makanan",
                "price_food" => 50000.00,
                "discount" => 5000.00,
    ]);
    Menu::create([
        "id" => 3,
                "id_admin" => 1,
                "name" => "Bakso",
                "desc" => "Bakso khas malang",
                "image_path" => "img/menu/1/fP1CJxSJLsNWxA4ybWlspnRtrNbX1wWRzqlQpMrD.png",
                "number_available" => 100,
                "number_sale" => 0,
                "favorite" => 0,
                "food_type" => "Makanan",
                "price_food" => 15000.00,
                "discount" => 0.00,
    ]);

    Menu::create([
        "id" => 4,
                "id_admin" => 1,
                "name" => "Mie ayam",
                "desc" => "Mie ayam Khas Wonogiri",
                "image_path" => "img/menu/1/fP1CJxSJLsNWxA4ybWlspnRtrNbX1wWRzqlQpMrD.png",
                "number_available" => 100,
                "number_sale" => 0,
                "favorite" => 0,
                "food_type" => "Makanan",
                "price_food" => 15000.00,
                "discount" => 0.00,
    ]);
    Menu::create([
        "id" => 5,
                "id_admin" => 1,
                "name" => "Nasi Goreng",
                "desc" => "Nasi Goreng",
                "image_path" => "img/menu/1/fP1CJxSJLsNWxA4ybWlspnRtrNbX1wWRzqlQpMrD.png",
                "number_available" => 100,
                "number_sale" => 0,
                "favorite" => 0,
                "food_type" => "Makanan",
                "price_food" => 12000.00,
                "discount" => 0.00,
    ]);
    }
}
