<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SiteSettingController extends Controller
{
    /**
     * Show the site settings form.
     */
    public function index()
    {
        $siteLogo = SiteSetting::get('site_logo');
        $heroImage = SiteSetting::get('hero_image');

        return view('admin.site-settings.index', compact('siteLogo', 'heroImage'));
    }

    /**
     * Update the site settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'site_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'hero_image' => 'nullable|image|mimes:png,jpg,jpeg,webp|max:5120',
        ]);

        // Handle Logo Upload
        if ($request->hasFile('site_logo')) {
            // Delete old logo if exists
            $oldLogo = SiteSetting::get('site_logo');
            if ($oldLogo && Storage::exists($oldLogo)) {
                Storage::delete($oldLogo);
            }

            $logoPath = $request->file('site_logo')->store('site');
            SiteSetting::set('site_logo', $logoPath);
        }

        // Handle Hero Image Upload
        if ($request->hasFile('hero_image')) {
            // Delete old hero image if exists
            $oldHero = SiteSetting::get('hero_image');
            if ($oldHero && Storage::exists($oldHero)) {
                Storage::delete($oldHero);
            }

            $heroPath = $request->file('hero_image')->store('site');
            SiteSetting::set('hero_image', $heroPath);
        }

        return redirect()->route('admin.site-settings.index')->with('success', 'Pengaturan website berhasil diperbarui!');
    }
}
