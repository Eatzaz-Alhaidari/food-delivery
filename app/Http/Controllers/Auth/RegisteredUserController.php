<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * عرض صفحة إنشاء حساب جديد
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * معالجة طلب إنشاء حساب جديد
     * ملاحظة: التسجيل العام ينشئ عميلاً فقط (UserRole::Customer) بشكل إجباري ومحمي تماماً
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = UserRole::Customer;
        $user->save();

        event(new Registered($user));

        Auth::login($user);

        return redirect('/');
    }
}
