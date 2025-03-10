<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $products = json_decode(Storage::get('products.json'), true);
            return response()->json($products);
        }

        return view('form');
    }
    public function submit(Request $request)
    {
        $data = $request->validate([
            'product_name' => 'required|string',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
        ]);

        $data['datetime'] = now()->toDateTimeString();
        $data['total_value'] = $data['quantity'] * $data['price'];

        $products = json_decode(Storage::get('products.json'), true);
        $products[] = $data;
        Storage::put('products.json', json_encode($products));

        return response()->json($products);
    }

    public function edit(Request $request, $id)
    {
        $products = json_decode(Storage::get('products.json'), true);

        if (isset($products[$id])) {
            $products[$id] = [
                'product_name' => $request->product_name,
                'quantity' => $request->quantity,
                'price' => $request->price,
                'datetime' => now()->toDateTimeString(),
                'total_value' => $request->quantity * $request->price,
            ];

            Storage::put('products.json', json_encode($products));
        }

        return response()->json($products);
    }
}