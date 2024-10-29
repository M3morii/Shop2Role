<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Stock;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['category', 'files']);
        
        // Handle search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }
        
        // Handle sorting
        if ($request->has('sort') && $request->has('order')) {
            $query->orderBy($request->input('sort'), $request->input('order'));
        }
        
        // Pagination
        $items = $query->paginate($request->input('per_page', 10));
        
        return response()->json($items);
    }

    // Metode baru untuk pencarian
    public function search(Request $request)
    {
        $search = $request->input('search');
        
        $query = Item::with(['files']);
        
        if ($search) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }
        
        $items = $query->get();
        
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
        try {
            DB::beginTransaction();
            
            // Validasi request
            $validated = $request->validate([
                'name' => 'required|string',
                'description' => 'required|string',
                'sellprice' => 'required|numeric',
                'stock' => 'required|numeric',
                'category_id' => 'required|exists:categories,id',
                'files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // Simpan item
            $item = Item::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'sellprice' => $validated['sellprice'],
                'stock' => $validated['stock'],
                'category_id' => $validated['category_id']
            ]);

            // Handle file upload
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('public/items');
                    
                    // Simpan informasi file ke database
                    File::create([
                        'item_id' => $item->id,
                        'file_path' => str_replace('public/', '', $path)
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Item berhasil ditambahkan', 'item' => $item->load('files')]);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error creating item: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal menambahkan item: ' . $e->getMessage()], 500);
        }
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
