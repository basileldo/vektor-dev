<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Vektor\Checkout\ShippingMethod;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $methods = [
            ['name' => 'Collection', 'code' => 'collection', 'description' => 'Collect from <a href="https://vektor.co.uk/contact/" target="_blank" class="text-primary">vektor</a>', 'price' => 0, 'configuration' => ['onecrm_shipping_provider_id' => 'f165f9e8-c364-7f2b-1282-646783251679'], 'is_active' => true],
            ['name' => 'Royal Mail', 'code' => 'royal_mail', 'description' => '3 to 5 working days', 'price' => 5, 'configuration' => ['onecrm_shipping_provider_id' => 'c86a2c23-27fd-14b3-848a-49f773bc527c', 'items_from' => 0, 'items_to' => 2]],
            ['name' => 'DPD', 'code' => 'dpd', 'description' => '1 to 2 working days', 'price' => 10, 'configuration' => ['onecrm_shipping_provider_id' => 'f3219a4a-670d-74ea-c06a-646783ae5bf7', 'items_from' => 0, 'items_to' => 9999]],
        ];

        foreach ($methods as $method) {
            ShippingMethod::create($method);
        }
    }
}
