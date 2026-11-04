<x-app-layout>
    <x-slot name="header">
        <h2 class="font-heading font-semibold text-20 text-sufra-text">
            الملف الشخصي
        </h2>
    </x-slot>

    <div class="py-6 space-y-6">
        <div class="p-5 sm:p-6 bg-sufra-surface border border-sufra-border rounded shadow-none">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="p-5 sm:p-6 bg-sufra-surface border border-sufra-border rounded shadow-none">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="p-5 sm:p-6 bg-sufra-surface border border-sufra-border rounded shadow-none">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
