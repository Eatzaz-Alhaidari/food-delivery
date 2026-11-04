<nav x-data="{ open: false }" class="bg-sufra-surface border-b border-sufra-border">
    <!-- قائمة التنقل الأساسية -->
    <div class="max-w-sufra mx-auto px-4 sm:px-6">
        <div class="flex justify-between h-16">
            <div class="flex items-center gap-8">
                <!-- الشعار -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}">
                        <x-application-logo />
                    </a>
                </div>

                <!-- روابط التنقل -->
                <div class="hidden sm:flex sm:items-center sm:gap-6 h-full">
                    <x-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        الرئيسية
                    </x-nav-link>
                    <x-nav-link :href="route('restaurants.index')" :active="request()->routeIs('restaurants.*')">
                        استكشف المطاعم
                    </x-nav-link>
                </div>
            </div>

            <!-- الجانب الأيسر: أيقونة السلة وقائمة المستخدم -->
            <div class="flex items-center gap-3">
                {{-- أيقونة السلة الحية المتزامنة --}}
                <livewire:customer.cart-icon />

                <!-- قائمة المستخدم للشاشات الكبيرة -->
                <div class="hidden sm:flex sm:items-center">
                    @auth
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center gap-2 px-3 py-2 border border-sufra-border rounded text-14 font-medium text-sufra-text bg-sufra-surface hover:bg-sufra-bg focus:outline-none transition-colors duration-150">
                                    <span>{{ Auth::user()->name }}</span>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-sufra-muted"></i>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2">
                                    <i data-lucide="user" class="w-4 h-4 text-sufra-muted"></i>
                                    <span>الملف الشخصي</span>
                                </x-dropdown-link>

                                @if (Auth::user()->isCustomer())
                                    <x-dropdown-link :href="route('orders.index')" class="flex items-center gap-2">
                                        <i data-lucide="shopping-bag" class="w-4 h-4 text-sufra-muted"></i>
                                        <span>طلباتي</span>
                                    </x-dropdown-link>
                                    <x-dropdown-link :href="route('addresses.index')" class="flex items-center gap-2">
                                        <i data-lucide="map-pin" class="w-4 h-4 text-sufra-muted"></i>
                                        <span>عناوين التوصيل</span>
                                    </x-dropdown-link>
                                @endif

                                @if (Auth::user()->isAdmin())
                                    <x-dropdown-link href="/admin" class="flex items-center gap-2">
                                        <i data-lucide="shield" class="w-4 h-4 text-sufra-muted"></i>
                                        <span>لوحة الإدارة</span>
                                    </x-dropdown-link>
                                @elseif (Auth::user()->isRestaurant())
                                    <x-dropdown-link href="/restaurant" class="flex items-center gap-2">
                                        <i data-lucide="store" class="w-4 h-4 text-sufra-muted"></i>
                                        <span>لوحة المطعم</span>
                                    </x-dropdown-link>
                                @endif

                                <!-- تسجيل الخروج -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();"
                                        class="flex items-center gap-2 text-sufra-accent">
                                        <i data-lucide="log-out" class="w-4 h-4 text-sufra-accent"></i>
                                        <span>تسجيل الخروج</span>
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    @else
                        <div class="flex items-center gap-2">
                            <x-button href="{{ route('login') }}" variant="secondary" size="sm">
                                تسجيل الدخول
                            </x-button>
                            <x-button href="{{ route('register') }}" variant="primary" size="sm">
                                إنشاء حساب
                            </x-button>
                        </div>
                    @endauth
                </div>

                <!-- زر القائمة للجوال -->
                <div class="flex items-center sm:hidden">
                    <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded border border-sufra-border text-sufra-text hover:bg-sufra-bg focus:outline-none">
                        <i data-lucide="menu" class="w-5 h-5" x-show="!open"></i>
                        <i data-lucide="x" class="w-5 h-5" x-show="open" style="display: none;"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- قائمة الجوال المنسدلة -->
    <div :class="{'block': open, 'hidden': ! open}"
        class="hidden sm:hidden border-t border-sufra-border bg-sufra-surface">
        <div class="py-2 space-y-1">
            <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                الرئيسية
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('restaurants.index')" :active="request()->routeIs('restaurants.*')">
                استكشف المطاعم
            </x-responsive-nav-link>
        </div>

        @auth
            <!-- معلومات المستخدم وخيارات الحساب -->
            <div class="pt-3 pb-3 border-t border-sufra-border px-4">
                <div class="font-medium text-16 text-sufra-text">{{ Auth::user()->name }}</div>
                <div class="text-14 text-sufra-muted">{{ Auth::user()->email }}</div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        الملف الشخصي
                    </x-responsive-nav-link>

                    @if (Auth::user()->isCustomer())
                        <x-responsive-nav-link :href="route('orders.index')">
                            طلباتي
                        </x-responsive-nav-link>
                        <x-responsive-nav-link :href="route('addresses.index')">
                            عناوين التوصيل
                        </x-responsive-nav-link>
                    @endif

                    @if (Auth::user()->isAdmin())
                        <x-responsive-nav-link href="/admin">
                            لوحة الإدارة
                        </x-responsive-nav-link>
                    @elseif (Auth::user()->isRestaurant())
                        <x-responsive-nav-link href="/restaurant">
                            لوحة المطعم
                        </x-responsive-nav-link>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();" class="text-sufra-accent">
                            تسجيل الخروج
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        @else
            <div class="p-4 border-t border-sufra-border space-y-2">
                <x-button href="{{ route('login') }}" variant="secondary" size="sm" class="w-full justify-center">
                    تسجيل الدخول
                </x-button>
                <x-button href="{{ route('register') }}" variant="primary" size="sm" class="w-full justify-center">
                    إنشاء حساب
                </x-button>
            </div>
        @endauth
    </div>
</nav>
