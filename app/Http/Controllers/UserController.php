<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        try {
            $users = User::all();
            return response()->json($users);
        } catch (\Exception $e) {
            \Log::error('Error loading users: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memuat daftar pengguna'
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->json(['message' => 'Pengguna berhasil diperbarui', 'user' => $user]);
    }

    public function changeRole($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Jika user adalah super admin (user pertama), jangan izinkan perubahan
            if ($user->id === 1) {
                return response()->json([
                    'message' => 'Tidak dapat mengubah role Super Admin'
                ], 403);
            }

            // Toggle role antara admin (1) dan customer (2)
            $newRole = $user->role_id === 1 ? 2 : 1;
            $user->role_id = $newRole;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Role berhasil diubah',
                'new_role' => $newRole
            ]);
        } catch (\Exception $e) {
            \Log::error('Error changing user role: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal mengubah role pengguna'
            ], 500);
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'Pengguna berhasil dihapus']);
    }
}
