<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserInformationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $states = ["Alaska", "Alabama", "Arkansas", "American Samoa", "Arizona", "California", "Colorado", "Connecticut", "District of Columbia", "Delaware", "Florida", "Georgia", "Guam", "Hawaii", "Iowa", "Idaho", "Illinois", "Indiana", "Kansas", "Kentucky", "Louisiana", "Massachusetts", "Maryland", "Maine", "Michigan", "Minnesota", "Missouri", "Mississippi", "Montana", "North Carolina", "North Dakota", "Nebraska", "New Hampshire", "New Jersey", "New Mexico", "Nevada", "New York", "Ohio", "Oklahoma", "Oregon", "Pennsylvania", "Puerto Rico", "Rhode Island", "South Carolina", "South Dakota", "Tennessee", "Texas", "Utah", "Virginia", "Virgin Islands", "Vermont", "Washington", "Wisconsin", "West Virginia", "Wyoming"];
        $types = ['customer', 'staff'];
        $cardTypes = ['visa', 'amex', 'mastercard'];
        $selectedCardType = current($this->faker->randomElements($cardTypes));
        $cardType = [
            'visa' => 'Visa',
            'mastercard' => 'MasterCard',
            'amex' => 'American Express',
        ];

        return [
            'type' => $this->faker->randomElement($types),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'middle_name' => $this->faker->name(),
            'telephone_number' => $this->faker->phoneNumber(),
            'mobile_number' => $this->faker->phoneNumber(),
            'is_active' => true,
            'notes' => 'Generated via UserInformationFactory at ' . now(),

            'billing_address' => $this->faker->streetName(),
            'billing_address_city' => $this->faker->city(),
            'billing_address_state' => $this->faker->randomElement($states),
            'billing_address_zipcode' => $this->faker->postcode(),
            'billing_address_country' => 'US',

            'shipping_address' => $this->faker->streetName(),
            'shipping_address_city' => $this->faker->city(),
            'shipping_address_state' => $this->faker->randomElement($states),
            'shipping_address_zipcode' => $this->faker->postcode(),
            'shipping_address_country' => 'US',

            'credit_card_type' => $selectedCardType,
            'credit_card_number' => $this->faker->creditCardNumber($cardType[$selectedCardType]),
            'credit_card_expiration_date' => $this->faker->creditCardExpirationDateString(true, 'm/Y'),
            'credit_card_cvv' => '123',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
