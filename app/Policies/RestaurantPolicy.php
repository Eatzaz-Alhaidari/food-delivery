<?php

namespace App\Policies;

use App\Models\Restaurant;
use App\Models\User;

class RestaurantPolicy
{
    /**
     * التحقق مما إذا كان المستخدم يملك صلاحية عرض بيانات هذا المطعم
     */
    public function view(User $user, Restaurant $restaurant): bool
    {
        return $user->id === $restaurant->user_id || $user->isAdmin();
    }

    /**
     * التحقق مما إذا كان المستخدم يملك صلاحية تعديل بيانات هذا المطعم
     */
    public function update(User $user, Restaurant $restaurant): bool
    {
        return $user->id === $restaurant->user_id || $user->isAdmin();
    }

    /**
     * التحقق مما إذا كان المستخدم يملك صلاحية إدارة قائمة طعام وتصنيفات هذا المطعم
     */
    public function manageMenu(User $user, Restaurant $restaurant): bool
    {
        return $user->id === $restaurant->user_id || $user->isAdmin();
    }
}

