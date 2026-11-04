<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * التحقق من دور المستخدم الحالي وصلاحيته للوصول
     * يقبل دوراً واحداً أو قائمة أدوار مسموح بها
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // إذا لم يكن المستخدم مسجلاً للدخول يُعاد توجيهه لصفحة الدخول
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // استخراج قيمة الدور الحالية كـ string
        $currentRole = $request->user()->role instanceof UserRole
            ? $request->user()->role->value
            : (string) $request->user()->role;

        // التحقق من تطابق دور المستخدم مع أي من الأدوار المطلوبة
        if (!in_array($currentRole, $roles, true)) {
            abort(403, 'عذراً، لا تملك الصلاحية اللازمة للوصول إلى هذه الصفحة.');
        }

        return $next($request);
    }
}
