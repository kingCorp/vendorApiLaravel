<?php

namespace App\Http\Controllers;

use App\Business;
use App\Http\Resources\BusinessResource;
use Illuminate\Http\Request;
use Validator;

class BusinessController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //get businesses
        $business = Business::paginate(15);

        //return business as resource
        return BusinessResource::collection($business);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validate
        $validator = Validator::make($request->all(), [
            'name_of_business' => 'required',
            'category' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        //create business
        $business = Business::create([
            'user_id' => $request->user()->id,
            'name_of_business' => $request->name_of_business,
            'category' => $request->category,
            'business_image' => $request->business_image,
            'description' => $request->description,
        ]);

        return new BusinessResource($business);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //get business
        $business = Business::find($id);

        if ($business == null) {
            return response()->json(['status' => 'Business was deleted'], 404);
        }
        //return single Business
        return new BusinessResource($business);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $business = Business::find($id);
        // check if currently authenticated user is the owner of the buisness
        if ($request->user()->id !== $business->user_id) {
            return response()->json(['error' => 'You can only edit your own business.'], 403);
        }

        $business->update($request->only(['name_of_business', 'category', 'description']));
        return response()->json(["success"=>"update sucessfull", $business]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        //get Business
        $business = Business::find($id);
        
        // check if currently authenticated user is the owner of the buisness
        if ($business->id == null) {
            return response()->json(['error' => 'business has been deleted or doesnt exist'], 403);
        }
        // check if currently authenticated user is the owner of the buisness
        if ($request->user()->id !== $business->user_id) {
            return response()->json(['error' => 'You can only edit your own business.'], 403);
        }
        //return single Business
        if ($business->delete()) {
            return response()->json(['status' => 'Business was deleted'], 200);
        }
    }

    //return searched categories
    public function category($category)
    {
        //get business
        $business = Business::where('category', $category)->get();

        if ($business == null) {
            return response()->json(['status' => 'Business category is not found'], 404);
        }
        //return single Business
        return response()->json($business);
    }

    public function uploadFile(Request $request)
    {

        if ($request->hasFile('image_file')) {
            \Cloudder::upload($request->file('image_file'));
            $c = \Cloudder::getResult();
            if ($c) {
                $business = Business::where('user_id', $request->user()->id)
                    ->update(['business_image' => $c['url']]);
                return response()->json(['success' => 'You have successfully upload images.', $business]);
            }

        }
    }

}
