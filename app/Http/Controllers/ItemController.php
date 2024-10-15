<?php
namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    // Menampilkan semua item (list)
    public function index()
    {
        $items = Item::all();
        return response()->json($items, 200);
    }

    // Menampilkan detail item berdasarkan ID (show)
    public function show($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        return response()->json($item, 200);
    }

    // Fungsi untuk menambah atau memperbarui item
    public function storeOrUpdate(Request $request, $id = null)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'sellprice' => 'required|numeric',
            'finalstock' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Jika ID ada, maka update; jika tidak, buat item baru
        $item = $id ? Item::find($id) : new Item;

        if ($id && !$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        $item->name = $request->name;
        $item->description = $request->description;
        $item->sellprice = $request->sellprice;
        $item->finalstock = $request->finalstock;

        if ($item->save()) {
            $message = $id ? 'Item updated successfully' : 'Item created successfully';
            return response()->json(['message' => $message, 'item' => $item], 200);
        }

        return response()->json(['message' => 'Error saving item'], 500);
    }

    // Fungsi untuk menghapus item
    public function destroy($id)
    {
        $item = Item::find($id);

        if (!$item) {
            return response()->json(['message' => 'Item not found'], 404);
        }

        if ($item->delete()) {
            return response()->json(['message' => 'Item deleted successfully'], 200);
        }

        return response()->json(['message' => 'Error deleting item'], 500);
    }
}
