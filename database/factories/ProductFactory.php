<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $purchase = $this->faker->numberBetween(50, 500);
        $retail = $purchase + $this->faker->numberBetween(20, 150);

        $hasSale = $this->faker->boolean(30); // 30% products on sale
        $trackStock = $this->faker->boolean(70); // most products track stock

        return [
            'product_name' => ucfirst($this->faker->words(rand(2, 4), true)),
            'category_id' => Category::inRandomOrder()->value('id'),
            'purchase_price' => $purchase,
            'retail_price' => $retail,
            'sale_price' => $hasSale
                ? $this->faker->numberBetween($purchase, $retail - 10)
                : null,

            'product_image' => null,

            'barcode' => $this->faker->ean13(),
            'sku' => strtoupper($this->faker->bothify('SKU-#######')),

            'track_stock' => $trackStock,
            'stock_quantity' => $trackStock
                ? $this->faker->numberBetween(0, 200)
                : 0,

            'min_stock_level' => $trackStock
                ? $this->faker->numberBetween(5, 20)
                : 0,

            'is_active' => true,
        ];
    }
}
