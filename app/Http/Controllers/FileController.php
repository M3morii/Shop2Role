<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;

class FileController extends Controller
{
    // Fungsi untuk menghapus file
    public function destroy($id)
    {
        $file = File::find($id);
        if (!$file) {
            return response()->json(['message' => 'File not found'], 404);
        }

        // Hapus file dari storage
        \Storage::disk('public')->delete($file->file_path);

        // Hapus record file dari database
        if ($file->delete()) {
            return response()->json(['message' => 'File deleted successfully'], 200);
        }

        return response()->json(['message' => 'Error deleting file'], 500);
    }
}
