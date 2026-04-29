<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'image',
        'description'
    ];

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('order');
    }

    public function sizes()
    {
        return $this->hasMany(ProductSize::class)->orderByRaw("FIELD(size, 'XS', 'S', 'M', 'L', 'XL', 'XXL')");
    }
}
