<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Shipping
            ['key' => 'delivery_charge_inside_dhaka', 'value' => '60', 'type' => 'number', 'group' => 'shipping', 'label' => 'Inside Dhaka Delivery Charge (৳)'],
            ['key' => 'delivery_charge_outside_dhaka', 'value' => '120', 'type' => 'number', 'group' => 'shipping', 'label' => 'Outside Dhaka Delivery Charge (৳)'],
            ['key' => 'free_shipping_threshold', 'value' => '1000', 'type' => 'number', 'group' => 'shipping', 'label' => 'Free Shipping Threshold (৳)'],

            // Site
            ['key' => 'site_name', 'value' => 'Glow & Glam', 'type' => 'string', 'group' => 'site', 'label' => 'Website Name'],
            ['key' => 'site_phone', 'value' => '+8801XXXXXXXXX', 'type' => 'string', 'group' => 'site', 'label' => 'Contact Phone'],
            ['key' => 'site_email', 'value' => 'info@glowglam.com', 'type' => 'string', 'group' => 'site', 'label' => 'Contact Email'],
            ['key' => 'site_address', 'value' => 'Dhaka, Bangladesh', 'type' => 'string', 'group' => 'site', 'label' => 'Shop Address'],

            // Couriers
            ['key' => 'couriers', 'value' => json_encode(['Pathao', 'RedX', 'Paperfly', 'Steadfast', 'eCourier']), 'type' => 'json', 'group' => 'shipping', 'label' => 'Available Couriers'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
