<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Stock;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    public function index()
    {
        $items = Item::with(['files'])->get();
        return response()->json($items, 200);
    }

    public function show($id)
    {
        $item = Item::with(['files'])->find($id);

        if (!$item) {
            return response()->json(['message' => 'Item tidak ditemukan'], 404);
        }

        return response()->json($item, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'sellprice' => 'required|numeric',
            'stock' => 'required|numeric',
            'files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $item = Item::create($request->only('name', 'description', 'sellprice', 'stock'));

        Stock::create([
            'item_id' => $item->id,
            'type' => 'in',
            'quantity' => $request->stock
        ]);

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

        return response()->json(['message' => 'Item berhasil dibuat', 'item' => $item], 201);
    }

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
            return response()->json(['message' => 'Item tidak ditemukan'], 404);
        }

        $item->update($request->only('name', 'description', 'sellprice'));

        $this->saveFiles($request, $item);

        return response()->json(['message' => 'Item berhasil diperbarui', 'item' => $item], 200);
    }

    public function destroy($id)
    {
        $item = Item::find($id);
        if (!$item) {
            return response()->json(['message' => 'Item tidak ditemukan'], 404);
        }

        File::where('item_id', $item->id)->delete();

        if ($item->delete()) {
            return response()->json(['message' => 'Item berhasil dihapus'], 200);
        }

        return response()->json(['message' => 'Terjadi kesalahan saat menghapus item'], 500);
    }

    private function saveFiles(Request $request, Item $item)
    {
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
    }
}
