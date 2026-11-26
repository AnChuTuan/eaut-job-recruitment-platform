<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Chặn truy cập nếu người dùng không có vai trò Quản trị viên (Admin).
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || !$request->user()->isAdmin()) {
            abort(403, 'Bạn không có quyền truy cập vào phân hệ Quản trị viên.');
        }

        return $next($request);
    }
}
