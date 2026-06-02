<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ListMaterial;
use Illuminate\Database\Seeder;

class ProductMaterialSeeder extends Seeder
{
    public function run(): void
    {
        // Buat Product Pintu Kayu
        $pintuKayu = Product::create([
            'name' => 'Pintu Kayu',
            'quantity' => 0,
        ]);

        // Buat Materials untuk Pintu Kayu
        ListMaterial::create([
            'product_id' => $pintuKayu->id,
            'name' => 'Kayu',
            'quantity' => 50,
            'quantity_needed' => 10,
        ]);

        ListMaterial::create([
            'product_id' => $pintuKayu->id,
            'name' => 'Gagang Pintu Kayu',
            'quantity' => 30,
            'quantity_needed' => 1,
        ]);

        // Update product quantity
        $pintuKayu->updateQuantity();

        // Buat Product Pintu Kaca
        $pintuKaca = Product::create([
            'name' => 'Pintu Kaca',
            'quantity' => 0,
        ]);

        // Buat Materials untuk Pintu Kaca
        ListMaterial::create([
            'product_id' => $pintuKaca->id,
            'name' => 'Kaca',
            'quantity' => 40,
            'quantity_needed' => 2,
        ]);

        ListMaterial::create([
            'product_id' => $pintuKaca->id,
            'name' => 'Gagang Pintu Kaca',
            'quantity' => 25,
            'quantity_needed' => 1,
        ]);

        // Update product quantity
        $pintuKaca->updateQuantity();
    }
}
