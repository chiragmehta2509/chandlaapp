<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorCategory;
use App\Models\VendorLead;
use App\Models\VendorPortfolioImage;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VendorWebController extends Controller
{
    /**
     * GET /client/vendors (directory list)
     */
    public function index(Request $request)
    {
        $categories = VendorCategory::orderBy('name', 'asc')->get();
        
        $query = Vendor::where('status', 'active')->with(['category', 'portfolioImages']);

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->input('category'));
        }

        // Filter by city
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . trim($request->input('city')) . '%');
        }

        // Filter by price tier
        if ($request->filled('price_tier')) {
            $query->where('price_tier', $request->input('price_tier'));
        }

        // Show featured first, then standard sorting
        $vendors = $query->orderBy('is_featured', 'desc')
            ->orderBy('business_name', 'asc')
            ->paginate(12)
            ->withQueryString();

        // Check if event context is passed
        $event = null;
        if ($request->filled('event_id')) {
            $event = Event::where('user_id', Auth::id())->find($request->input('event_id'));
        }

        return view('client.vendors.index', compact('vendors', 'categories', 'event'));
    }

    /**
     * GET /client/vendors/{vendor}
     */
    public function show(Request $request, Vendor $vendor)
    {
        if ($vendor->status !== 'active') {
            abort(404);
        }

        $event = null;
        if ($request->filled('event_id')) {
            $event = Event::where('user_id', Auth::id())->find($request->input('event_id'));
        }

        $vendor->load(['category', 'portfolioImages']);

        return view('client.vendors.show', compact('vendor', 'event'));
    }

    /**
     * POST /client/vendors/{vendor}/lead
     */
    public function submitLead(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'event_id' => 'nullable|exists:events,id',
            'host_name' => 'required|string|max:255',
            'host_phone' => 'required|string|max:20',
            'message' => 'nullable|string',
        ]);

        $lead = VendorLead::create([
            'vendor_id' => $vendor->id,
            'event_id' => $validated['event_id'],
            'host_name' => $validated['host_name'],
            'host_phone' => $validated['host_phone'],
            'message' => $validated['message'],
            'source' => 'directory',
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lead logged successfully.',
                'lead_id' => $lead->id
            ]);
        }

        // Redirect to WhatsApp chat or back with success
        $whatsappMessage = "Hello " . $vendor->business_name . ", I found your profile on Chandla Book. I would like to inquire about your services.";
        if ($vendor->whatsapp) {
            $url = "https://wa.me/" . preg_replace('/[^0-9]/', '', $vendor->whatsapp) . "?text=" . urlencode($whatsappMessage);
            return redirect()->away($url);
        }

        return redirect()->back()->with('success', 'Lead logged! You can contact the vendor directly at ' . $vendor->phone);
    }

    /**
     * GET /client/vendors/register
     */
    public function registerForm()
    {
        $categories = VendorCategory::orderBy('name', 'asc')->get();
        return view('client.vendors.register', compact('categories'));
    }

    /**
     * POST /client/vendors/register
     */
    public function registerSubmit(Request $request)
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
        $vendorData['status'] = 'pending';
        $vendorData['is_featured'] = false;
        $vendorData['is_verified'] = false;

        $vendor = Vendor::create($vendorData);

        // Handle portfolio images
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

        return redirect()->route('client.vendors.register')
            ->with('success', 'Your business profile has been submitted successfully! An admin will review and activate it shortly.');
    }

    /**
     * GET /admin/vendors (Admin panel page)
     */
    public function adminIndex(Request $request)
    {
        $pendingVendors = Vendor::where('status', 'pending')->with('category')->get();
        $activeVendors = Vendor::where('status', 'active')->with('category')->get();
        
        $leads = VendorLead::with(['vendor', 'event'])->orderBy('created_at', 'desc')->get();

        return view('admin.vendors.index', compact('pendingVendors', 'activeVendors', 'leads'));
    }

    /**
     * POST /admin/vendors/{vendor}/approve
     */
    public function adminApprove(Vendor $vendor)
    {
        $vendor->update(['status' => 'active']);
        return redirect()->back()->with('success', $vendor->business_name . ' has been approved and is now active.');
    }

    /**
     * POST /admin/vendors/{vendor}/reject
     */
    public function adminReject(Vendor $vendor)
    {
        $vendor->update(['status' => 'inactive']);
        return redirect()->back()->with('success', $vendor->business_name . ' has been marked inactive/rejected.');
    }
}
