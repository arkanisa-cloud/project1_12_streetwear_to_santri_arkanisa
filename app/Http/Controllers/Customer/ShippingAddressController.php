<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ShippingAddress;
use App\Services\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ShippingAddressController
 * Controller untuk mengelola alamat pengiriman customer
 * Terintegrasi dengan RajaOngkir API untuk dependent dropdown Province -> City
 */
class ShippingAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     * List semua alamat customer
     */
    public function index()
    {
        $addresses = Auth::user()->shippingAddresses;
        return view('customer.shipping-addresses.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('customer.shipping-addresses.create');
    }

    /**
     * Store a newly created resource in storage.
     * Simpan alamat baru (dengan province_id & city_id dari RajaOngkir)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'required|string',
            'province_id' => 'required|integer',
            'province' => 'required|string|max:100',
            'city_id' => 'required|integer',
            'city' => 'required|string|max:100',
            'district_id' => 'required|integer',
            'district' => 'required|string|max:100',
            'subdistrict_id' => 'nullable|integer',
            'subdistrict' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:10',
        ]);

        // Map district_id (Kecamatan) to subdistrict_id in database for RajaOngkir cost compatibility
        $dataToSave = $validated;
        $dataToSave['subdistrict_id'] = $validated['district_id'];
        $dataToSave['subdistrict'] = $validated['district'] . ($validated['subdistrict'] ? ', ' . $validated['subdistrict'] : '');

        Auth::user()->shippingAddresses()->create($dataToSave);

        if ($request->has('redirect') && $request->redirect === 'checkout') {
            return redirect()
                ->route('customer.checkout.index')
                ->with('success', 'Alamat berhasil ditambahkan.');
        }

        return redirect()
            ->route('customer.addresses.index')
            ->with('success', 'Alamat berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShippingAddress $shippingAddress)
    {
        // Cek ownership
        if ($shippingAddress->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('customer.shipping-addresses.edit', compact('shippingAddress'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ShippingAddress $shippingAddress)
    {
        // Cek ownership
        if ($shippingAddress->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'address' => 'required|string',
            'province_id' => 'required|integer',
            'province' => 'required|string|max:100',
            'city_id' => 'required|integer',
            'city' => 'required|string|max:100',
            'district_id' => 'required|integer',
            'district' => 'required|string|max:100',
            'subdistrict_id' => 'nullable|integer',
            'subdistrict' => 'nullable|string|max:100',
            'postal_code' => 'required|string|max:10',
        ]);

        // Map district_id (Kecamatan) to subdistrict_id in database for RajaOngkir cost compatibility
        $dataToSave = $validated;
        $dataToSave['subdistrict_id'] = $validated['district_id'];
        $dataToSave['subdistrict'] = $validated['district'] . ($validated['subdistrict'] ? ', ' . $validated['subdistrict'] : '');

        $shippingAddress->update($dataToSave);

        if ($request->has('redirect') && $request->redirect === 'checkout') {
            return redirect()
                ->route('customer.checkout.index')
                ->with('success', 'Alamat berhasil diupdate.');
        }

        return redirect()
            ->route('customer.addresses.index')
            ->with('success', 'Alamat berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShippingAddress $shippingAddress)
    {
        // Cek ownership
        if ($shippingAddress->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Cek apakah alamat dipakai di order aktif
        if ($shippingAddress->orders()->whereIn('status', ['pending', 'processed', 'shipped'])->count() > 0) {
            return back()->with('error', 'Tidak bisa menghapus alamat yang digunakan di order aktif.');
        }

        $shippingAddress->delete();

        return redirect()
            ->route('customer.addresses.index')
            ->with('success', 'Alamat berhasil dihapus.');
    }
}

