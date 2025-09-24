<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
    // Create a new store
    public function create(Request $request)
    {
        // Validate request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Create store for logged-in user
        $store = Store::create([
            'name' => $request->name,
            'owner_id' => Auth::id()
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Store created successfully',
            'data' => $store
        ]);
    }

    // List all stores for the logged-in user
    public function index()
    {
        $stores = Store::where('owner_id', Auth::id())->get();

        return response()->json([
            'status' => true,
            'data' => $stores
        ]);
    }

    // Fetch single store by id
    public function show($id)
    {
        $store = Store::where('id', $id)->where('owner_id', Auth::id())->first();

        if (!$store) {
            return response()->json([
                'status' => false,
                'message' => 'Store not found'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $store
        ]);
    }
}
