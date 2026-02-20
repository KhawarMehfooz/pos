<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $topLevelCategories = Category::factory(7)->create();

        // this will create sub categories
        Category::factory(3)->sequence(fn($seq) => [
            'parent_id' => $topLevelCategories->random()->id,
        ])->create();
    }
}
