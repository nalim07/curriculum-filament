<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPanelPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $panel)
    {
        $user = Auth::user();

        $permissions = [
            'admin' => 'can_access_panel_admin',
            'curriculum' => 'can_access_panel_curriculum',
            'admission' => 'can_access_panel_admission',
            'teacher' => 'can_access_panel_teacher',
            'teacher_pg_kg' => 'can_access_panel_teacher_pg_kg',
            'student' => 'can_access_panel_student',
        ];

        if ($user->hasRole('super_admin')) {
            return $next($request);
        } elseif ($user && isset($permissions[$panel]) && $user->can($permissions[$panel])) {
            return $next($request);
        }

        abort(403, 'Unauthorized');
    }
}
