<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRestaurantRequest;
use App\Http\Requests\UpdateRestaurantLocationsRequest;
use App\Http\Requests\UpdateRestaurantRequest;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use App\Models\RestaurantLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestaurantController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $restaurants = Restaurant::with(['locations'])->get();
        if ($restaurants) {
            return ApiResponse::sendResponse(200, 'All Restaurants',$restaurants);
        }
        return ApiResponse::sendResponse(404, 'There are no  restaurants');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreRestaurantRequest $request)
    {
        try {
            $validatedData = $request->validated();
            $logoPath = null;
            $coverPath = null;

            DB::beginTransaction();

            $restaurant = Restaurant::create([
                'user_id' => $validatedData['user_id'],
                'slug' => $validatedData['slug'],
                'title' => $validatedData['title'],
                'name' => $validatedData['name'],
                'summary' => $validatedData['summary'],
                'description' => $validatedData['description'],
                'status' => $validatedData['status'] ?? 'Active',
            ]);

            if ($request->has('locations')) {
                foreach ($validatedData['locations'] as $locationData) {
                    RestaurantLocation::create([
                        'restaurant_id' => $restaurant->id,
                        'address' => $locationData['address'],
                        'country_id' => $locationData['country_id'],
                        'governorate_id' => $locationData['governorate_id'],
                        'city_id' => $locationData['city_id'],
                        'state_id' => $locationData['state_id'],
                        'zip' => $locationData['zip'] ?? null,
                        'latitude' => $locationData['latitude'] ?? null,
                        'longitude' => $locationData['longitude'] ?? null,
                        'opening_time' => $locationData['opening_time'] ?? null,
                        'closed_time' => $locationData['closed_time'] ?? null,
                        'closed_days' => $locationData['closed_days'] ? implode(',', $locationData['closed_days']) : null,
                        'number_of_tables' => $locationData['number_of_tables'] ?? 0,
                        'phone_number' => $locationData['phone_number'] ?? null,
                        'mobile_number' => $locationData['mobile_number'] ?? null,
                        'hot_line' => $locationData['hot_line'] ?? null,
                        'status' => $locationData['status'] ?? 'Opened',
                    ]);
                }
            }

            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $logoName = time() . '_logo.' . $logo->getClientOriginalExtension();

                if (!$logo->move(public_path('images'), $logoName)) {
                    DB::rollback();
                    return ApiResponse::sendResponse(500, 'Failed to upload logo');
                }

                $logoPath = $logoName;
                $restaurant->logo = $logoPath;
            }

            if ($request->hasFile('cover')) {
                $cover = $request->file('cover');
                $coverName = time() . '_cover.' . $cover->getClientOriginalExtension();

                if (!$cover->move(public_path('images'), $coverName)) {
                    DB::rollback();
                    return ApiResponse::sendResponse(500, 'Failed to upload cover');
                }

                $coverPath = $coverName;
                $restaurant->cover = $coverPath;
            }

            $restaurant->save();
            DB::commit();
            return ApiResponse::sendResponse(201, 'Restaurant Created Successfully', new RestaurantResource($restaurant));
        } catch (\Throwable $e) {
            DB::rollback();
            return ApiResponse::sendResponse(500, 'Failed to create restaurant', ['error' => $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        $restaurant = Restaurant::with(['locations', 'categories'])->findOrFail($id);
        if($restaurant){
            return ApiResponse::sendResponse(200, 'Restaurant', new RestaurantResource($restaurant));
        }
        return ApiResponse::sendResponse(404, 'Can`t find this Restaurant');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRestaurantRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $logoPath = null;
            $coverPath = null;

            DB::beginTransaction();

            $restaurant = Restaurant::findOrFail($id);

            $restaurant->fill([
                'user_id' => $validatedData['user_id'],
                'slug' => $validatedData['slug'],
                'title' => $validatedData['title'],
                'name' => $validatedData['name'],
                'summary' => $validatedData['summary'],
                'description' => $validatedData['description'],
                'status' => $validatedData['status'] ?? 'Active',
            ]);

            $restaurant->save();

            if ($request->has('locations')) {
                foreach ($validatedData['locations'] as $locationData) {

                    if (isset($locationData['id'])) {
                        $location = RestaurantLocation::findOrFail($locationData['id']);
                        $location->update([
                            'address' => $locationData['address'],
                            'country_id' => $locationData['country_id'],
                            'governorate_id' => $locationData['governorate_id'],
                            'city_id' => $locationData['city_id'],
                            'state_id' => $locationData['state_id'],
                            'zip' => $locationData['zip'] ?? null,
                            'latitude' => $locationData['latitude'] ?? null,
                            'longitude' => $locationData['longitude'] ?? null,
                            'opening_time' => $locationData['opening_time'] ?? null,
                            'closed_time' => $locationData['closed_time'] ?? null,
                            'closed_days' => $locationData['closed_days'] ? implode(',', $locationData['closed_days']) : null,
                            'number_of_tables' => $locationData['number_of_tables'] ?? 0,
                            'phone_number' => $locationData['phone_number'] ?? null,
                            'mobile_number' => $locationData['mobile_number'] ?? null,
                            'hot_line' => $locationData['hot_line'] ?? null,
                            'status' => $locationData['status'] ?? 'Opened',
                        ]);
                    }
                }
            }

            if ($request->hasFile('logo')) {
                $logo = $request->file('logo');
                $logoName = time() . '_logo.' . $logo->getClientOriginalExtension();

                if (!$logo->move(public_path('images'), $logoName)) {
                    DB::rollback();
                    return ApiResponse::sendResponse(500, 'Failed to upload logo');
                }

                $logoPath = $logoName;
                $restaurant->logo = $logoPath;
            }

            if ($request->hasFile('cover')) {
                $cover = $request->file('cover');
                $coverName = time() . '_cover.' . $cover->getClientOriginalExtension();

                if (!$cover->move(public_path('images'), $coverName)) {
                    DB::rollback();
                    return ApiResponse::sendResponse(500, 'Failed to upload cover');
                }

                $coverPath = $coverName;
                $restaurant->cover = $coverPath;
            }

            $restaurant->save();

            DB::commit();
            return ApiResponse::sendResponse(200, 'Restaurant Updated Successfully', new RestaurantResource($restaurant));
        } catch (\Throwable $e) {
            DB::rollback();
            return ApiResponse::sendResponse(500, 'Failed to update restaurant', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $restaurant = Restaurant::findOrFail($id);

            DB::beginTransaction();
            $restaurant->delete();

            DB::commit();
            return ApiResponse::sendResponse(200, 'Restaurant Deleted Successfully');
        } catch (\Throwable $e) {
            DB::rollback();
            return ApiResponse::sendResponse(500, 'Failed to delete restaurant', ['error' => $e->getMessage()]);
        }
    }

    public function getRestaurantsByUserId(string $user_id)
    {
        $restaurants = Restaurant::with('locations')->where('user_id', $user_id)->get();

        if ($restaurants->isEmpty()) {
            return ApiResponse::sendResponse(404, 'No restaurants found for this user.');
        }

        return ApiResponse::sendResponse(200, 'Restaurants', RestaurantResource::collection($restaurants));
    }


    public function updateLocations(UpdateRestaurantLocationsRequest $request, $restaurantId)
    {
        try {
            $validatedData = $request->validated(); 

            DB::beginTransaction();

            foreach ($validatedData['locations'] as $locationData) {
                $location = RestaurantLocation::findOrFail($locationData['id']);
                $location->update([
                    'address' => $locationData['address'],
                    'country_id' => $locationData['country_id'],
                    'governorate_id' => $locationData['governorate_id'],
                    'city_id' => $locationData['city_id'],
                    'state_id' => $locationData['state_id'],
                    'zip' => $locationData['zip'] ?? null,
                    'latitude' => $locationData['latitude'] ?? null,
                    'longitude' => $locationData['longitude'] ?? null,
                    'opening_time' => $locationData['opening_time'] ?? null,
                    'closed_time' => $locationData['closed_time'] ?? null,
                    'closed_days' => $locationData['closed_days'] ? implode(',', $locationData['closed_days']) : null,
                    'number_of_tables' => $locationData['number_of_tables'] ?? 0,
                    'phone_number' => $locationData['phone_number'] ?? null,
                    'mobile_number' => $locationData['mobile_number'] ?? null,
                    'hot_line' => $locationData['hot_line'] ?? null,
                    'status' => $locationData['status'] ?? 'Opened',
                ]);
            }

            DB::commit();

            return ApiResponse::sendResponse(200, 'Locations Updated Successfully');
        } catch (\Throwable $e) {
            DB::rollback();
            return ApiResponse::sendResponse(500, 'Failed to update locations', ['error' => $e->getMessage()]);
        }
    }
}
