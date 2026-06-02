<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'quantity'];

    // Relasi dengan materials
    public function materials()
    {
        return $this->hasMany(ListMaterial::class);
    }

    // Relasi dengan orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Hitung quantity product berdasarkan materials tersedia
    public function calculateAvailableQuantity(): int
    {
        $materials = $this->materials()->get();
        
        if ($materials->isEmpty()) {
            return 0;
        }

        // Cari minimum dari (quantity / quantity_needed) dari semua materials
        $minQuantity = PHP_INT_MAX;
        
        foreach ($materials as $material) {
            if ($material->quantity_needed <= 0) {
                continue;
            }
            $available = (int)floor($material->quantity / $material->quantity_needed);
            $minQuantity = min($minQuantity, $available);
        }

        return $minQuantity === PHP_INT_MAX ? 0 : $minQuantity;
    }

    // Update quantity product
    public function updateQuantity(): void
    {
        $this->quantity = $this->calculateAvailableQuantity();
        $this->save();
    }
}
