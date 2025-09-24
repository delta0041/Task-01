<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BusinessDetail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BusinessDetailController extends Controller
{
    /**
     * Create or update business details
     */
    public function createOrUpdate(Request $request)
    {
        // Validate inputs
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|integer|exists:stores,id',
            'business_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'gst_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048', // max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle logo upload
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        // Update or create business detail
        $businessDetail = BusinessDetail::updateOrCreate(
            ['store_id' => $request->store_id],
            [
                'business_name' => $request->business_name,
                'owner_name' => $request->owner_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'gst_number' => $request->gst_number,
                'address' => $request->address,
                'logo' => $logoPath ?? BusinessDetail::where('store_id', $request->store_id)->value('logo'),
            ]
        );

        return response()->json([
            'status' => true,
            'message' => 'Business details saved successfully',
            'data' => $businessDetail
        ]);
    }

    /**
     * Fetch business details for a store
     */
    public function show(Request $request)
    {
        $user = $request->user();

        // Fetch all stores owned by the logged-in user
        $stores = \App\Models\Store::where('owner_id', $user->id)->get();

        if ($stores->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No stores found for this user'
            ], 404);
        }

        // Fetch business details for all those stores
        $storeIds = $stores->pluck('id'); // extract all store IDs
        $businessDetails = BusinessDetail::whereIn('store_id', $storeIds)->get();

        if ($businessDetails->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'No business details found for these stores'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'stores' => $stores,
            'business_details' => $businessDetails
        ]);
    }

    /**
     * Delete business details for a store
     */
    public function destroy(Request $request, $storeId)
    {
        $businessDetail = BusinessDetail::where('store_id', $storeId)->first();

        if (!$businessDetail) {
            return response()->json([
                'status' => false,
                'message' => 'Business details not found'
            ], 404);
        }

        // Delete logo file if exists
        if ($businessDetail->logo && Storage::disk('public')->exists($businessDetail->logo)) {
            Storage::disk('public')->delete($businessDetail->logo);
        }

        $businessDetail->delete();

        return response()->json([
            'status' => true,
            'message' => 'Business profile deleted successfully'
        ]);
    }

}
