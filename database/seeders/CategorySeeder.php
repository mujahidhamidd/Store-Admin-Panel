<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        $category = Category::create([
            'name' => 'products'
        ]);

        $category->subCategories()->createMany(
            [
                [
                    'name' => 'Mobile Phones'
                ],
                [
                    'name' => 'Laptops'
                ],
                [
                    'name' => 'Head Phones'
                ]
            ]
        );

        $category = Category::create([
            'name' => 'CPU'
        ]);

        $category->subCategories()->createMany(
            [
                [
                    'name' => 'AMD'
                ],
                [
                    'name' => 'Intel'
                ],
                [
                    'name' => 'Snapdragon'
                ],
                [
                    'name' => 'Exynos'
                ]
            ]
        );
    }
}
