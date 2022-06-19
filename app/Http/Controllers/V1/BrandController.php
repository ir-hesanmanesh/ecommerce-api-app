<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BrandController extends ApiController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brand = Brand::paginate(2);

        return $this->successResponse([
            'brand' => BrandResource::collection($brand),
            'links' => BrandResource::collection($brand)->response()->getData()->links,
            'meta' => BrandResource::collection($brand)->response()->getData()->meta,

        ], 200, 'get list of brands successfuly');
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
            'name' => 'required',
            'display_name' => 'required|unique:brands'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();

        $brand = Brand::create([
            'name' => $request->name,
            'display_name' => $request->display_name
        ]);

        DB::commit();

        return $this->successResponse(new BrandResource($brand), 201, 'create brand successfuly');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Brand $brand)
    {
        return $this->successResponse(new BrandResource($brand), 200, 'get of a brand show successfuly');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Brand $brand)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'display_name' => 'required|unique:brands'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }

        DB::beginTransaction();

        $brand->update([
            'name' => $request->name,
            'display_name' => $request->display_name
        ]);


        DB::commit();

        return $this->successResponse(new BrandResource($brand), 200, 'update brand successfuly');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();

        return $this->successResponse(new BrandResource($brand), 200, 'delete brand successfuly');
    }

    public function products(Brand $brand)
    {
        return $this->successResponse(new BrandResource($brand->load('products')), 200, 'get of a brand show all products successfuly');
    }
}
