<?php

namespace App\Livewire\Admin;

use App\Enums\UserRole;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class RestaurantManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all'; // all, active, inactive

    // حالة نافذة إضافة مطعم جديد مع مالكه
    public bool $showCreateModal = false;
    public string $ownerName = '';
    public string $ownerEmail = '';
    public string $ownerPassword = '';
    public string $name = '';
    public string $cuisine = '';
    public string $district = '';
    public ?string $description = '';
    public float $minOrder = 30.0;
    public float $deliveryFee = 15.0;
    public int $prepTime = 25;

    // حالة نافذة تعديل مطعم قائم
    public bool $showEditModal = false;
    public ?int $editingRestaurantId = null;

    public ?string $feedbackMessage = null;

    /**
     * تبديل حالة التفعيل (نشط / معطّل) فورياً بدون إعادة تحميل
     */
    public function toggleActive(int $restaurantId): void
    {
        $restaurant = Restaurant::findOrFail($restaurantId);
        $restaurant->update(['is_active' => ! $restaurant->is_active]);

        $statusText = $restaurant->is_active ? 'تفعيل' : 'إيقاف';
        $this->feedbackMessage = "تم {$statusText} مطعم \"{$restaurant->name}\" بنجاح.";
    }

    /**
     * فتح نافذة إضافة مطعم جديد
     */
    public function openCreateModal(): void
    {
        $this->reset([
            'ownerName', 'ownerEmail', 'ownerPassword',
            'name', 'cuisine', 'district', 'description',
            'minOrder', 'deliveryFee', 'prepTime',
            'feedbackMessage',
        ]);
        $this->minOrder = 30.0;
        $this->deliveryFee = 15.0;
        $this->prepTime = 25;
        $this->resetValidation();
        $this->showCreateModal = true;
    }

    /**
     * حفظ مطعم جديد وإنشاء حساب لمالكه داخل DB::transaction (المتطلب 3)
     */
    public function createRestaurant(): void
    {
        $validated = $this->validate([
            'ownerName' => 'required|string|min:3|max:100',
            'ownerEmail' => 'required|email|max:150|unique:users,email',
            'ownerPassword' => 'required|string|min:6|max:100',
            'name' => 'required|string|min:2|max:100',
            'cuisine' => 'required|string|min:2|max:50',
            'district' => 'required|string|min:2|max:100',
            'description' => 'nullable|string|max:1000',
            'minOrder' => 'required|numeric|min:0|max:1000',
            'deliveryFee' => 'required|numeric|min:0|max:100',
            'prepTime' => 'required|integer|min:5|max:180',
        ], [
            'ownerName.required' => 'اسم مالك المطعم مطلوب.',
            'ownerEmail.required' => 'البريد الإلكتروني لحساب المالك مطلوب.',
            'ownerEmail.unique' => 'البريد الإلكتروني مسجل مسبقاً بحساب آخر.',
            'ownerPassword.required' => 'كلمة المرور لحساب المالك مطلوبة.',
            'ownerPassword.min' => 'يجب ألا تقل كلمة المرور عن 6 خانات.',
            'name.required' => 'اسم المطعم مطلوب.',
            'cuisine.required' => 'نوع المطبخ أو التصنيف مطلوب.',
            'district.required' => 'اسم الحي مطلوب.',
            'minOrder.required' => 'الحد الأدنى للطلب مطلوب.',
            'deliveryFee.required' => 'رسوم التوصيل مطلوبة.',
            'prepTime.required' => 'وقت التجهيز المتوقع مطلوب.',
        ]);

        DB::transaction(function () use ($validated) {
            // ١. إنشاء حساب المستخدم وتعيين دوره restaurant بأمان
            $owner = new User();
            $owner->forceFill([
                'name' => $validated['ownerName'],
                'email' => $validated['ownerEmail'],
                'password' => Hash::make($validated['ownerPassword']),
                'role' => UserRole::Restaurant,
                'email_verified_at' => now(),
            ])->save();

            // ٢. توليد slug فريد للمطعم
            $baseSlug = Str::slug($validated['name'], '-', 'ar');
            if (empty($baseSlug)) {
                $baseSlug = 'restaurant-' . Str::random(5);
            }
            $slug = $baseSlug;
            $counter = 1;
            while (Restaurant::where('slug', $slug)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }

            // ٣. إنشاء المطعم وربطه بمالكه
            Restaurant::create([
                'user_id' => $owner->id,
                'name' => $validated['name'],
                'slug' => $slug,
                'description' => $validated['description'] ?? null,
                'cuisine' => $validated['cuisine'],
                'district' => $validated['district'],
                'min_order' => $validated['minOrder'],
                'delivery_fee' => $validated['deliveryFee'],
                'prep_time' => $validated['prepTime'],
                'is_open' => true,
                'is_active' => true,
            ]);
        });

        $this->showCreateModal = false;
        $this->feedbackMessage = 'تمت إضافة المطعم وإنشاء حساب المالك بنجاح.';
    }

    /**
     * فتح نافذة تعديل مطعم
     */
    public function openEditModal(int $restaurantId): void
    {
        $restaurant = Restaurant::findOrFail($restaurantId);

        $this->editingRestaurantId = $restaurant->id;
        $this->name = $restaurant->name;
        $this->cuisine = $restaurant->cuisine;
        $this->district = $restaurant->district;
        $this->description = $restaurant->description ?? '';
        $this->minOrder = (float) $restaurant->min_order;
        $this->deliveryFee = (float) $restaurant->delivery_fee;
        $this->prepTime = (int) $restaurant->prep_time;

        $this->resetValidation();
        $this->showEditModal = true;
    }

    /**
     * حفظ تعديلات المطعم
     */
    public function updateRestaurant(): void
    {
        $validated = $this->validate([
            'name' => 'required|string|min:2|max:100',
            'cuisine' => 'required|string|min:2|max:50',
            'district' => 'required|string|min:2|max:100',
            'description' => 'nullable|string|max:1000',
            'minOrder' => 'required|numeric|min:0|max:1000',
            'deliveryFee' => 'required|numeric|min:0|max:100',
            'prepTime' => 'required|integer|min:5|max:180',
        ], [
            'name.required' => 'اسم المطعم مطلوب.',
            'cuisine.required' => 'نوع المطبخ مطلوب.',
            'district.required' => 'اسم الحي مطلوب.',
        ]);

        $restaurant = Restaurant::findOrFail($this->editingRestaurantId);
        $restaurant->update([
            'name' => $validated['name'],
            'cuisine' => $validated['cuisine'],
            'district' => $validated['district'],
            'description' => $validated['description'] ?? null,
            'min_order' => $validated['minOrder'],
            'delivery_fee' => $validated['deliveryFee'],
            'prep_time' => $validated['prepTime'],
        ]);

        $this->showEditModal = false;
        $this->editingRestaurantId = null;
        $this->feedbackMessage = "تم تحديث بيانات مطعم \"{$restaurant->name}\" بنجاح.";
    }

    public function closeModal(): void
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->editingRestaurantId = null;
        $this->resetValidation();
    }

    public function render()
    {
        $query = Restaurant::with(['user', 'categories.menuItems'])
            ->latest();

        if (! empty(trim($this->search))) {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                    ->orWhere('district', 'like', $searchTerm)
                    ->orWhere('cuisine', 'like', $searchTerm);
            });
        }

        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        $restaurants = $query->paginate(10);

        return view('livewire.admin.restaurant-manager', [
            'restaurants' => $restaurants,
        ])->layout('layouts.admin', ['title' => 'إدارة المطاعم']);
    }
}

