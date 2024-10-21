<?php

namespace App\Http\Controllers;

use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    /**
     * Menghapus file dari storage dan database.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $file = File::find($id);

        if (!$file) {
            return response()->json(['message' => 'File tidak ditemukan'], 404);
        }

        if (Storage::disk('public')->delete($file->file_path) && $file->delete()) {
            return response()->json(['message' => 'File berhasil dihapus'], 200);
        }

        return response()->json(['message' => 'Gagal menghapus file'], 500);
    }
}
