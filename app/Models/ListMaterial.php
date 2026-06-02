<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListMaterial extends Model
{
    use HasFactory;

    protected $table = 'list_materials';
    protected $fillable = ['product_id', 'name', 'quantity', 'quantity_needed'];

    // Relasi dengan product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Update quantity product ketika material berubah
    protected static function booted()
    {
        static::updated(function (self $material) {
            $material->product->updateQuantity();
        });

        static::created(function (self $material) {
            $material->product->updateQuantity();
        });

        static::deleted(function (self $material) {
            $material->product->updateQuantity();
        });
    }
}
