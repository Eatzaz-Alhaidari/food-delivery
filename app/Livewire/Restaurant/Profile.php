<?php

namespace App\Livewire\Restaurant;

use App\Models\Restaurant;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public Restaurant $restaurant;

    public string $name = '';
    public ?string $description = '';
    public string $cuisine = '';
    public string $district = '';
    public float $min_order = 0;
    public float $delivery_fee = 0;
    public int $prep_time = 30;
    public bool $is_open = true;

    public $logo;
    public $cover;

    public ?string $successMessage = null;

    /**
     * قواعد التحقق لبيانات المطعم
     */
    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'cuisine' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'min_order' => ['required', 'numeric', 'min:0', 'max:1000'],
            'delivery_fee' => ['required', 'numeric', 'min:0', 'max:500'],
            'prep_time' => ['required', 'integer', 'min:5', 'max:180'],
            'logo' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
            'cover' => ['nullable', 'image', 'max:3072', 'mimes:jpg,jpeg,png,webp'],
        ];
    }

    /**
     * أسماء الحقول المعربة لرسائل الخطأ
     */
    protected $validationAttributes = [
        'name' => 'اسم المطعم',
        'description' => 'وصف المطعم',
        'cuisine' => 'نوع المطبخ',
        'district' => 'الحي',
        'min_order' => 'الحد الأدنى للطلب',
        'delivery_fee' => 'رسوم التوصيل',
        'prep_time' => 'وقت التحضير',
        'logo' => 'الشعار',
        'cover' => 'صورة الغلاف',
    ];

    /**
     * تهيئة المكون مع فحص الصلاحية عبر Policy
     */
    public function mount(): void
    {
        $user = auth()->user();
        $this->restaurant = $user->restaurant ?? abort(403, 'عذراً، لا يوجد مطعم مسجل مرتبط بحسابك.');

        // فحص Policy لمنع أي وصول غير مصرح
        $this->authorize('update', $this->restaurant);

        $this->name = $this->restaurant->name;
        $this->description = $this->restaurant->description;
        $this->cuisine = $this->restaurant->cuisine;
        $this->district = $this->restaurant->district;
        $this->min_order = (float) $this->restaurant->min_order;
        $this->delivery_fee = (float) $this->restaurant->delivery_fee;
        $this->prep_time = (int) $this->restaurant->prep_time;
        $this->is_open = (bool) $this->restaurant->is_open;
    }

    /**
     * تبديل حالة فتح وإغلاق المطعم فورياً وبدون إعادة تحميل
     */
    public function toggleOpen(): void
    {
        $this->authorize('update', $this->restaurant);

        $this->restaurant->is_open = ! $this->restaurant->is_open;
        $this->restaurant->save();
        $this->is_open = $this->restaurant->is_open;

        $this->dispatch('lucide:refresh');
        $this->successMessage = $this->is_open
            ? 'تم فتح المطعم وبدء استقبال الطلبات.'
            : 'تم إغلاق المطعم مؤقتاً وإيقاف استقبال الطلبات.';
    }

    /**
     * حفظ بيانات المطعم ورفع الشعار والغلاف
     */
    public function save(): void
    {
        $this->authorize('update', $this->restaurant);
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'cuisine' => $this->cuisine,
            'district' => $this->district,
            'min_order' => $this->min_order,
            'delivery_fee' => $this->delivery_fee,
            'prep_time' => $this->prep_time,
        ];

        // رفع الشعار إن وُجد وحفظه في مجلد التخزين العام
        if ($this->logo) {
            $data['logo'] = $this->logo->store('restaurants', 'public');
        }

        // رفع الغلاف إن وُجد
        if ($this->cover) {
            $data['cover'] = $this->cover->store('restaurants', 'public');
        }

        $this->restaurant->update($data);

        // تصفير ملفات الرفع المؤقتة
        $this->logo = null;
        $this->cover = null;

        $this->successMessage = 'تم تحديث بيانات المطعم بنجاح.';
        $this->dispatch('lucide:refresh');
    }

    public function render()
    {
        return view('livewire.restaurant.profile')
            ->layout('layouts.restaurant', ['title' => 'بيانات المطعم']);
    }
}

