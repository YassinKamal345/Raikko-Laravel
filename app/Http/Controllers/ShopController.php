<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->search) {
            $query->where(
                'name',
                'like',
                '%' . $request->search . '%'
            );
        }

        $products = $query->get();

        return view('shop', compact('products'));
    }
}