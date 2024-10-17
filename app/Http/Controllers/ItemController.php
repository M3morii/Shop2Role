<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Stock;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    // Menampilkan semua item (list)
    public function index()
    {
        $items = Item::with(['files'])->get();
        return response()->json($items, 200);
    }

    // Menampilkan detail item berdasarkan ID (show)
    public function show($id)
    {
        $item = Item::with(['files'])->find($id);

        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        return response()->json($item, 200);
    }

    // Fungsi untuk menambah item
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'sellprice' => 'required|numeric',
            'stock' => 'required|numeric',
            'type' => 'required',
            'quantity' => 'required',
            'files' => 'nullable',
            'files.*' => 'file|mimes:jpeg,png,jpg,gif,mp4|max:2048' // Atur sesuai dengan kebutuhan
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $item = Item::create([
            'name' => $request->name,
            'description' => $request->description,
            'sellprice' => $request->sellprice,
            'stock' => $request->stock,
        ]);

        Stock::create([
            'item_id' => $item->id,
            'type' => $request->type,
            'quantity' => $request->quantity
        ]);




        // Menyimpan file jika ada
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filePath = $file->store('uploads', 'public');
                File::create([
                    'item_id' => $item->id,
                    'file_path' => $filePath,
                ]);
            }
        }

        return response()->json(['message' => 'Item created successfully', 'item' => $item], 201);
    }

    // Fungsi untuk memperbarui item
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'sellprice' => 'nullable|numeric',
            'files' => 'nullable|array',
            'files.*' => 'file|mimes:jpeg,png,jpg,gif,mp4|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $item = Item::find($id);
        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        // Update item jika ada perubahan
        $item->update($request->only('name', 'description', 'sellprice'));

        // Menyimpan file baru jika ada
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $filePath = $file->store('uploads', 'public');
                File::create([
                    'item_id' => $item->id,
                    'file_path' => $filePath,
                    'file_type' => $file->getClientOriginalExtension(),
                ]);
            }
        }

        return response()->json(['message' => 'Item updated successfully', 'item' => $item], 200);
    }

    // Fungsi untuk menghapus item
    public function destroy($id)
    {
        $item = Item::find($id);
        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        // Hapus file yang terkait
        File::where('item_id', $item->id)->delete();

        // Hapus item
        if ($item->delete()) {
            return response()->json(['message' => 'Item deleted successfully'], 200);
        }

        return response()->json(['message' => 'Error deleting item'], 500);
    }
}
