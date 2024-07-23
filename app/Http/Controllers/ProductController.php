<?php

namespace App\Http\Controllers;

use App\Models\Product;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;

class ProductController extends Controller
{
    //this method will show the products page
    public function index(){
        $products = Product::orderBy('created_at','DESC')->get();
        return view("products.list",[
            "products"=> $products
        ]);

    }

    //this page will create a product page
    public function create(){
        return view("products.create");

    }

    //this method will store a pruduct in db
    public function store(Request $request){

        $rules = [
            "name"=> "required | min:5",
            "price"=> "required | numeric",
            "sku"=> "required | min:3"
        ] ;

            if($request->image !=""){
                $rules["image"] = "image";
            }

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()){
            return redirect()->route("products.create")->withErrors($validator)->withInput();
    }

    //here we will insert product in db
    $product = new Product();
    $product->name = $request->name;
    $product->price = $request->price;
    $product->sku = $request->sku;
    $product->description = $request->description;
    $product->save();

    if($request->image != ""){
        //here we will store image
    $image = $request->image;
    $ext = $image->getClientOriginalExtension();
    $imageName = time().".".$ext; //unique image name

    //save image to product directory

    $image->move(public_path("uploads/products"), $imageName);

    //save image name in db
    $product->image = $imageName;
    $product->save();
    }


    return redirect()->route("products.index")->with("success","Product added Successfully.");



}

    //this method will show edit product page
    public function edit($id){
        $product = Product::findOrFail($id);
        return view("products.edit",[
            "product"=> $product
        ]);


    }

    //this method will update a product
    public function update($id, Request $request){
        $product = Product::findOrFail($id);
        $rules = [
            "name"=> "required | min:5",
            "price"=> "required | numeric",
            "sku"=> "required | min:3"
        ] ;

            if($request->image !=""){
                $rules["image"] = "image";
            }

        $validator = Validator::make($request->all(),$rules);

        if($validator->fails()){
            return redirect()->route("products.edit", $product->id)->withErrors($validator)->withInput();
    }

    //here we will update product
    $product->name = $request->name;
    $product->price = $request->price;
    $product->sku = $request->sku;
    $product->description = $request->description;
    $product->save();

    if($request->image != ""){
    //delete old image
    File::delete(public_path("uploads/products/".$product->image));

    //here we will store image
    $image = $request->image;
    $ext = $image->getClientOriginalExtension();
    $imageName = time().".".$ext; //unique image name

    //save image to product directory

    $image->move(public_path("uploads/products"), $imageName);

    //save image name in db
    $product->image = $imageName;
    $product->save();
    }


    return redirect()->route("products.index")->with("success","Product updated Successfully.");



    }

    //this method will delete a product
    public function destroy($id){
        $product = Product::findOrFail($id);

        //delete image
    File::delete(public_path("uploads/products/".$product->image));

        //delete product from database
        $product->delete();
    return redirect()->route("products.index")->with("success","Product deleted Successfully.");

    }


}
