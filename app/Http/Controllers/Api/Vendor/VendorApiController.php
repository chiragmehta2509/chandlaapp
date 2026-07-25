<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorCategory;
use App\Models\VendorLead;
use App\Models\VendorPortfolioImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VendorApiController extends Controller
{
    // Helper to check admin access
    protected function checkAdmin(Request $request)
    {
        $user = $request->user();
        if (!$user || !$user->is_admin) {
            return false;
        }
        return true;
    }

    /**
     * GET /api/vendor-categories
     */
    public function indexCategory()
    {
        $categories = VendorCategory::orderBy('name', 'asc')->get();
        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * GET /api/vendors
     */
    public function index(Request $request)
    {
        // Only active/approved vendors for public view
        $query = Vendor::where('status', 'active')->with(['category', 'portfolioImages']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('category_slug')) {
            $slug = $request->input('category_slug');
            $query->whereHas('category', function($q) use ($slug) {
                $q->where('slug', $slug);
            });
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . trim($request->input('city')) . '%');
        }

        if ($request->filled('price_tier')) {
            $query->where('price_tier', $request->input('price_tier'));
        }

        // Show featured first
        $vendors = $query->orderBy('is_featured', 'desc')
            ->orderBy('business_name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $vendors
        ]);
    }

    /**
     * GET /api/vendors/{id}
     */
    public function show($id)
    {
        $vendor = Vendor::with(['category', 'portfolioImages'])->find($id);

        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $vendor
        ]);
    }

    /**
     * POST /api/vendors (self-registration)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:vendor_categories,id',
            'business_name' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'price_tier' => 'required|in:budget,mid,premium',
            'description' => 'nullable|string',
            'phone' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'images' => 'nullable|array|max:6',
            'images.*' => 'image|max:4096'
        ]);

        $vendorData = collect($validated)->except(['images'])->toArray();
        $vendorData['status'] = 'pending'; // Requires manual approval
        $vendorData['is_featured'] = false;
        $vendorData['is_verified'] = false;

        $vendor = Vendor::create($vendorData);

        // Portfolio images upload
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $path = $file->store('vendors/portfolio', 'public');
                VendorPortfolioImage::create([
                    'vendor_id' => $vendor->id,
                    'image_url' => $path,
                    'sort_order' => $index
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Registration submitted successfully. Pending admin approval.',
            'data' => $vendor->load('portfolioImages')
        ], 201);
    }

    /**
     * POST /api/vendors/{id}/leads (log lead)
     */
    public function storeLead(Request $request, $id)
    {
        $vendor = Vendor::find($id);
        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        $validated = $request->validate([
            'event_id' => 'nullable|exists:events,id',
            'host_name' => 'required|string|max:255',
            'host_phone' => 'required|string|max:20',
            'message' => 'nullable|string',
            'source' => 'nullable|string|max:50'
        ]);

        $leadData = array_merge($validated, [
            'vendor_id' => $vendor->id,
            'source' => $validated['source'] ?? 'directory'
        ]);

        $lead = VendorLead::create($leadData);

        return response()->json([
            'success' => true,
            'message' => 'Lead logged successfully',
            'data' => $lead
        ], 201);
    }

    /**
     * GET /api/admin/vendors/pending
     */
    public function adminPending(Request $request)
    {
        if (!$this->checkAdmin($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized admin access'
            ], 403);
        }

        $pending = Vendor::where('status', 'pending')->with('category')->get();

        return response()->json([
            'success' => true,
            'data' => $pending
        ]);
    }

    /**
     * POST /api/admin/vendors/{id}/approve
     */
    public function adminApprove(Request $request, $id)
    {
        if (!$this->checkAdmin($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized admin access'
            ], 403);
        }

        $vendor = Vendor::find($id);
        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        $vendor->update(['status' => 'active']);

        return response()->json([
            'success' => true,
            'message' => 'Vendor approved successfully',
            'data' => $vendor
        ]);
    }

    /**
     * POST /api/admin/vendors/{id}/reject
     */
    public function adminReject(Request $request, $id)
    {
        if (!$this->checkAdmin($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized admin access'
            ], 403);
        }

        $vendor = Vendor::find($id);
        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Vendor not found'
            ], 404);
        }

        $vendor->update(['status' => 'inactive']);

        return response()->json([
            'success' => true,
            'message' => 'Vendor rejected/marked inactive successfully',
            'data' => $vendor
        ]);
    }
}
