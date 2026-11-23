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
        return $this->hasMany(ProductSize::class)->orderByRaw("CASE size WHEN 'XS' THEN 1 WHEN 'S' THEN 2 WHEN 'M' THEN 3 WHEN 'L' THEN 4 WHEN 'XL' THEN 5 WHEN 'XXL' THEN 6 END");
    }
}
