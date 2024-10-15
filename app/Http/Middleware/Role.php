<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roleId)
    {
        // Mendapatkan pengguna yang sedang login
        $user = Auth::user();

        // Memastikan pengguna terautentikasi
        if (!$user) {
            return response()->json(['message' => 'User not authenticated.'], 401);
        }

        // Memastikan pengguna memiliki role_id yang diminta
        if ($user->role_id != $roleId) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        return $next($request);
    }
}
