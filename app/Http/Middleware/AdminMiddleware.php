<?php
// app/Http/Middleware/AdminMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();

        // Разрешаем доступ админам и модераторам
        if (! $user->hasRole(['admin', 'moderator'])) {
            abort(403, 'Доступ запрещен. Требуются права администратора или модератора.');
        }

        return $next($request);
    }
}
