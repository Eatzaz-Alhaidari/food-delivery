<?php

namespace App\Enums;

enum UserRole: string
{
    case Customer = 'customer';
    case Restaurant = 'restaurant';
    case Admin = 'admin';

    /**
     * المسمى بالعربية لكل دور
     */
    public function label(): string
    {
        return match ($this) {
            self::Customer => 'عميل',
            self::Restaurant => 'مطعم',
            self::Admin => 'مدير المنصة',
        };
    }

    /**
     * المسار الافتراضي بعد تسجيل الدخول حسب الدور
     */
    public function defaultRoute(): string
    {
        return match ($this) {
            self::Admin => '/admin',
            self::Restaurant => '/restaurant',
            self::Customer => '/',
        };
    }
}

