<section class="space-y-5">
    <header class="pb-3 border-b border-sufra-border">
        <h2 class="font-heading font-semibold text-16 text-sufra-text">
            حذف الحساب
        </h2>

        <p class="mt-1 text-14 text-sufra-muted">
            بمجرد حذف حسابك، سيتم حذف كافة البيانات المرتبطة به نهائياً ولا يمكن استرجاعها.
        </p>
    </header>

    <div>
        <button
            type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="inline-flex items-center gap-1.5 px-4 py-2 rounded border border-sufra-accent bg-transparent text-sufra-accent text-14 font-medium hover:bg-sufra-accent hover:text-sufra-surface transition-colors"
        >
            <i data-lucide="trash-2" class="w-4 h-4"></i>
            <span>حذف الحساب نهائياً</span>
        </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 space-y-4">
            @csrf
            @method('delete')

            <h2 class="font-heading font-semibold text-16 text-sufra-text">
                هل أنت متأكد من رغبتك في حذف الحساب؟
            </h2>

            <p class="text-14 text-sufra-muted leading-relaxed">
                يرجى إدخال كلمة المرور لتأكيد رغبتك في حذف الحساب نهائياً.
            </p>

            <div>
                <x-input-label for="password" value="كلمة المرور" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="block w-full sm:w-3/4"
                    placeholder="أدخل كلمة المرور الحالية للتأكيد..."
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1" />
            </div>

            <div class="pt-3 border-t border-sufra-border flex justify-end gap-3">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close')">
                    إلغاء
                </x-button>

                <x-button type="submit" variant="danger" icon="trash-2">
                    تأكيد حذف الحساب
                </x-button>
            </div>
        </form>
    </x-modal>
</section>
