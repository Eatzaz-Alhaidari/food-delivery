<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading font-semibold text-20 text-sufra-text">
            لوحة التحكم
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="bg-sufra-surface border border-sufra-border rounded p-6 space-y-4">
            <h3 class="font-heading font-semibold text-16 text-sufra-text">
                مرحباً بك، {{ Auth::user()->name }}!
            </h3>
            <p class="text-14 text-sufra-muted">
                تم تسجيل دخولك بنجاح في منصة سُفرة. يمكنك استكشاف المطابخ المتاحة وبدء طلبك أو إدارة طلباتك السابقة.
            </p>
            <div class="pt-2 flex flex-wrap gap-3">
                <x-button href="{{ route('restaurants.index') }}" variant="primary" icon="store">
                    استكشف المطاعم
                </x-button>
                <x-button href="{{ route('orders.index') }}" variant="secondary" icon="shopping-bag">
                    طلباتي
                </x-button>
            </div>
        </div>
    </div>
</x-app-layout>
