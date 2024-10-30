<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Stock;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Item::with(['category', 'files']);

            // Filter berdasarkan pencarian
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function($q) use ($searchTerm) {
                    $q->where('name', 'like', "%{$searchTerm}%")
                      ->orWhere('description', 'like', "%{$searchTerm}%");
                });
            }

            // Filter berdasarkan kategori
            if ($request->has('category_id') && !empty($request->category_id)) {
                $query->where('category_id', $request->category_id);
            }

            // Ambil data dengan pagination
            $items = $query->orderBy('created_at', 'desc')
                          ->paginate(10);

            return response()->json($items);
        } catch (\Exception $e) {
            \Log::error('Error loading items: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memuat data item'
            ], 500);
        }
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
                    $path = $file->store('items', 'public');
                    
                    // Simpan informasi file ke database
                    File::create([
                        'item_id' => $item->id,
                        'file_path' => $path
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
        try {
            DB::beginTransaction();
            
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'sellprice' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:2048',
                'replace_images' => 'required|in:0,1'
            ]);

            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()], 422);
            }

            $item = Item::find($id);
            if (!$item) {
                return response()->json(['message' => 'Item tidak ditemukan'], 404);
            }

            // Update data item
            $item->update([
                'name' => $request->name,
                'description' => $request->description,
                'sellprice' => $request->sellprice,
                'category_id' => $request->category_id
            ]);

            // Handle file upload jika ada file baru dan replace_images = 1
            if ($request->hasFile('files') && $request->replace_images === '1') {
                // Hapus file lama
                foreach ($item->files as $file) {
                    Storage::disk('public')->delete($file->file_path);
                    $file->delete();
                }
                
                // Upload file baru
                foreach ($request->file('files') as $file) {
                    $path = $file->store('items', 'public');
                    File::create([
                        'item_id' => $item->id,
                        'file_path' => $path
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'Item berhasil diperbarui',
                'item' => $item->fresh()->load('files')
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error updating item: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memperbarui item: ' . $e->getMessage()
            ], 500);
        }
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

    public function deleteFile($itemId, $fileId)
    {
        try {
            // Cari file berdasarkan item_id dan file_id
            $file = File::where('item_id', $itemId)
                        ->where('id', $fileId)
                        ->firstOrFail();

            // Hapus file fisik dari storage
            if (Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
            
            // Hapus record dari database
            $file->delete();

            return response()->json([
                'success' => true,
                'message' => 'File berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            \Log::error('Error deleting file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus file: ' . $e->getMessage()
            ], 500);
        }
    }
}
