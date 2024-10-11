<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index()
{
    $items = Item::all();

    $formattedItems = $items->map(function ($item) {
        $file = str_replace('public','storage',$item->file);
        return [
            'id' => $item->id,
            'name' => $item->name,
            'description' => $item->description,
            'price' => 'RP ' . number_format($item->price, 0, ',', '.'),
            'stock' => $item->stock,
            'file' => url( $file), // Menghasilkan URL lengkap untuk file
        ];
    });

    return response()->json($formattedItems);
}

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string',
        'description' => 'required|string',
        'price' => 'required|numeric',
        'stock' => 'required|integer',
        'file' => 'required|image',
    ]);

    $path = $request->file('file')->store('public/images');

    $item = Item::create([
        'name' => $request->name,
        'description' => $request->description,
        'price' => $request->price,
        'stock' => $request->stock,
        'file' => $path,
    ]);

    return response()->json($item, 201);
}

public function update(Request $request, $id)
{
    // Temukan item atau kembalikan 404
    $item = Item::findOrFail($id);

    // Validasi
    $request->validate([
        'name' => 'string',
        'description' => 'string',
        'price' => 'numeric',
        'stock' => 'integer',
        'file' => 'image', // Validasi hanya jika file ada
    ]);

    // Periksa apakah ada file baru
    if ($request->hasFile('file')) {
        // Simpan file baru
        $path = $request->file('file')->store('images');
        $item->file = $path; // Update path file ke yang baru
    }

    // Update hanya field yang terisi dalam request
    $item->update($request->only('name', 'description', 'price', 'stock'));

    return response()->json($item);
}

public function destroy($id)
{
    $item = Item::findOrFail($id); // Temukan item atau kembalikan 404
    $item->delete();

    return response()->json(null, 204);
}

}
