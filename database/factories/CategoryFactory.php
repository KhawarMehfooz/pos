<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_name' => fake()->unique()->randomElement([
                'Electronics',
                'Beverages',
                'Food',
                'Clothing',
                'Accessories',
                'Dairy',
                'Bakery',
                'Snacks',
                'Household',
                'Personal Care'
            ]),
            'parent_id' => null,
            'is_active' => true
        ];
    }

    // state for creating a subcategory
    public function subcategory(int $parentId): static
    {
        return $this->state(fn(array $attributes) => [
            'parend_id' => $parentId
        ]);
    }

    // state for inactive category
    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false
        ]);
    }
}
