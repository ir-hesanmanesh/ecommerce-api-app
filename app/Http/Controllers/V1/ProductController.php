<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::paginate(8);

        return $this->successResponse([
            'products' => ProductResource::collection($products->load('images')),
            'links' => ProductResource::collection($products)->response()->getData()->links,
            'meta' => ProductResource::collection($products)->response()->getData()->meta,

        ], 200, 'get successfuly list of products');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'required|string',
            'brand_id' => 'required|integer',
            'category_id' => 'required|integer',
            'primary_image' => 'required|image',
            'description' => 'required',
            'price' => 'integer',
            'quantity' => 'integer',
            'delivery_amount' => 'nullable|integer',
            'description' => 'required',
            'images.*' => 'nullable|image'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();

        $fileNamePrimaryImage = genarateFileNames($request->primary_image->extension());
        $request->primary_image->storeAs('images/products', $fileNamePrimaryImage, 'public');

        if ($request->has('images')) {

            $FileNameImages = [];

            foreach ($request->images as $image) {
                $fileNameImage = genarateFileNames($image->extension());
                $image->storeAs('images/products', $fileNameImage, 'public');
                array_push($FileNameImages, $fileNameImage);
            }
        }

        $product = Product::create([
            'name' => $request->name,
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'primary_image' => $fileNamePrimaryImage,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'delivery_amount' => $request->delivery_amount,

        ]);

        if ($request->has('images')) {
            foreach ($FileNameImages as $image) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $image
                ]);
            }
        }

        DB::commit();

        return $this->successResponse(new ProductResource($product), 201, 'create product successfuly');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return $this->successResponse(new ProductResource($product->load('images')), 200, 'show product successfuly');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'required|string',
            'brand_id' => 'required|integer',
            'category_id' => 'required|integer',
            'primary_image' => 'nullable|image',
            'description' => 'required',
            'price' => 'integer',
            'quantity' => 'integer',
            'delivery_amount' => 'nullable|integer',
            'description' => 'required',
            'images.*' => 'nullable|image'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();

        if ($request->has('primary_image')) {
            $fileNamePrimaryImage = genarateFileNames($request->primary_image->extension());
            $request->primary_image->storeAs('images/products', $fileNamePrimaryImage, 'public');
        }

        if ($request->has('images')) {

            $fileNamesImages = [];

            foreach ($request->images as $image) {
                $fileNameImagePath = genarateFileNames($image->extension());
                $image->storeAs('images/products', $fileNameImagePath, 'public');
                array_push($fileNamesImages, $fileNameImagePath);
            }
        }

        $product->update([
            'name' => $request->name,
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'primary_image' => $request->has('primary_image') ? $fileNamePrimaryImage : $product->primary_image,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'delivery_amount' => $request->delivery_amount,
        ]);

        if ($request->has('images')) {

            foreach ($product->images as $image) {
                $image->delete();
            }

            foreach ($fileNamesImages as $image) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $image
                ]);
            }
        }

        DB::commit();

        return $this->successResponse(new ProductResource($product), 200, 'update product successfuly');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        DB::beginTransaction();

        $product->delete();

        DB::commit();

        return $this->successResponse(new ProductResource($product), 200, 'delete product successfuly');
    }
}
