<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coin;
use Illuminate\Http\Request;

class CoinController extends Controller
{
    public function index()
    {
        $coins = Coin::all();
        return view('admin.coins.index', compact('coins'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:gold,silver',
            'weight_in_grams' => 'required|numeric|min:0',
            'margin' => 'required|numeric',
        ]);
        $data['is_active'] = $request->has('is_active');

        Coin::create($data);
        return redirect()->route('admin.coins.index')->with('success', 'Coin created successfully.');
    }

    public function update(Request $request, Coin $coin)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:gold,silver',
            'weight_in_grams' => 'required|numeric|min:0',
            'margin' => 'required|numeric',
        ]);
        $data['is_active'] = $request->has('is_active');

        $coin->update($data);
        return redirect()->route('admin.coins.index')->with('success', 'Coin updated successfully.');
    }

    public function destroy(Coin $coin)
    {
        $coin->delete();
        return redirect()->route('admin.coins.index')->with('success', 'Coin deleted successfully.');
    }
}
