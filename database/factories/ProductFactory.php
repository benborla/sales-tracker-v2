<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'store_id' => $this->faker->biasedNumberBetween(4, 6),
            'name'  => $this->faker->word(),
            'upc' => $this->cleanUuid($this->faker->uuid(), 10),
            'asin' => $this->cleanUuid($this->faker->uuid(), 10),
            'retail_price' => $this->faker->randomFloat(2, 100, 1000),
            'reseller_price' => $this->faker->randomFloat(2, 100, 1000),
            'product_image' => $this->faker->word(),
            'weight_value' => $this->faker->numberBetween(1, 50),
            'weight_unit' => $this->faker->randomElement(['lbs', 'oz', 'ml/oz']),
            'active' => true,
            'notes' => 'Generated via Factory seeder',
            'manufactured_date' => $this->faker->date('Y-m-d H:i:s'),
            'sku' => $this->cleanUuid($this->faker->uuid(), 20),
            'size' => $this->faker->numberBetween(1, 20),
            'made_from' => $this->faker->randomElement(['U.S', 'China', 'Thailand', 'Vietname']),
            'total_inventory_remaining' => $this->faker->numberBetween(1, 250),
            'created_by' => 3,
            'updated_by' => 3
        ];
    }

    private function cleanUuid(string $uuid, $length = 20): string
    {
        $uuid = str_replace('-', '', $uuid);

        return substr($uuid, 1, $length);
    }
}
