<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TradingSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = TradingSetting::all()->pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'gold_base_price' => 'nullable|numeric',
            'silver_base_price' => 'nullable|numeric',
            'gst_percentage' => 'nullable|numeric',
            'tds_percentage' => 'nullable|numeric',
            'primary_color' => 'nullable|string',
            'secondary_color' => 'nullable|string',
            'price_source' => 'nullable|string|in:manual,api',
        ]);

        foreach ($data as $key => $value) {
            TradingSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }
}
