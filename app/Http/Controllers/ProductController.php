<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (file_get_contents('products.json') == null){
            $products = [];
        }else {
            $products = json_decode(file_get_contents('products.json'), true);
        }

        return view('product' ,compact('products'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = $request->validate([
            'product_name' => 'required',
            'quantity' => 'required|numeric',
            'price' => 'required|numeric',
        ]);

        $product['date'] = Carbon::now()->toDateTimeString();

        if (file_get_contents('products.json') == null){
            $products = [];
        }else {
            $products = json_decode(file_get_contents('products.json'), true);
        }

        array_push($products , $product);

        file_put_contents('products.json', json_encode($products));

//        return $products;

        return response()->json(['status' => true, 'data' => $products]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (file_get_contents('products.json') == null){
            $products = [];
        }else {
            $products = json_decode(file_get_contents('products.json'), true);
        }

        $product = $products[$id];

        return response()->json(['data' => $product]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (file_get_contents('products.json') == null){
            $products = [];
        }else {
            $products = json_decode(file_get_contents('products.json'), true);
        }

        foreach ($products as $key => $value) {
            if ($key == $id) {
                $products[$key]['product_name'] = $request->product_name;
                $products[$key]['quantity'] = $request->quantity;
                $products[$key]['price'] = $request->price;
            }
        }

        file_put_contents('products.json', json_encode($products));

        return redirect()->route('product.index')->with('status' , 'updated successfully');
    }

}
