<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function discover()
    {
        return view('discover');
    }

    public function product($id)
    {
        // In a real implementation, you would fetch the product by ID
        // For now, we'll return a placeholder view
        $product = \App\Models\Product::with(['edition.card', 'edition.set'])->find($id);

        if (!$product) {
            abort(404, 'Product not found');
        }

        return view('product', ['product' => $product]);
    }
}
