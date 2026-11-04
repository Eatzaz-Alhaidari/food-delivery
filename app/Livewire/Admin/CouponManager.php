<?php

namespace App\Livewire\Admin;

use App\Models\Coupon;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class CouponManager extends Component
{
    use WithPagination;

    public string $search = '';

    // حالة نموذج الإضافة والتعديل
    public bool $showModal = false;
    public ?int $editingCouponId = null;

    public string $code = '';
    public string $type = 'percent'; // percent, fixed
    public float $value = 10.0;
    public ?float $minOrder = null;
    public ?string $expiresAt = null;
    public ?int $maxUses = null;
    public bool $isActive = true;

    public ?string $feedbackMessage = null;

    public function toggleActive(int $couponId): void
    {
        $coupon = Coupon::findOrFail($couponId);
        $coupon->update(['is_active' => ! $coupon->is_active]);

        $statusText = $coupon->is_active ? 'تفعيل' : 'إيقاف';
        $this->feedbackMessage = "تم {$statusText} الكوبون \"{$coupon->code}\" بنجاح.";
    }

    public function openCreateModal(): void
    {
        $this->reset([
            'editingCouponId', 'code', 'minOrder', 'expiresAt', 'maxUses', 'feedbackMessage',
        ]);
        $this->type = 'percent';
        $this->value = 10.0;
        $this->isActive = true;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEditModal(int $couponId): void
    {
        $coupon = Coupon::findOrFail($couponId);

        $this->editingCouponId = $coupon->id;
        $this->code = $coupon->code;
        $this->type = $coupon->type;
        $this->value = (float) $coupon->value;
        $this->minOrder = $coupon->min_order ? (float) $coupon->min_order : null;
        $this->expiresAt = $coupon->expires_at ? $coupon->expires_at->format('Y-m-d') : null;
        $this->maxUses = $coupon->max_uses;
        $this->isActive = (bool) $coupon->is_active;

        $this->resetValidation();
        $this->showModal = true;
    }

    public function saveCoupon(): void
    {
        $codeRule = 'required|string|min:3|max:30|alpha_dash|unique:coupons,code';
        if ($this->editingCouponId) {
            $codeRule .= ',' . $this->editingCouponId;
        }

        $validated = $this->validate([
            'code' => $codeRule,
            'type' => 'required|in:percent,fixed',
            'value' => 'required|numeric|min:0.5' . ($this->type === 'percent' ? '|max:100' : ''),
            'minOrder' => 'nullable|numeric|min:0',
            'expiresAt' => 'nullable|date',
            'maxUses' => 'nullable|integer|min:1',
        ], [
            'code.required' => 'كود الكوبون مطلوب.',
            'code.unique' => 'كود الكوبون مسجل مسبقاً، يرجى اختيار كود آخر.',
            'code.alpha_dash' => 'كود الكوبون يجب أن يحتوي أحرفاً أو أرقاماً أو شرطات فقط.',
            'value.required' => 'قيمة الخصم مطلوبة.',
            'value.max' => 'نسبة الخصم المئوية لا يمكن أن تتجاوز 100%.',
        ]);

        $payload = [
            'code' => strtoupper(trim($validated['code'])),
            'type' => $validated['type'],
            'value' => $validated['value'],
            'min_order' => $validated['minOrder'] ?: null,
            'expires_at' => $validated['expiresAt'] ? Carbon::parse($validated['expiresAt'])->endOfDay() : null,
            'maxUses' => $validated['maxUses'] ?: null,
            'is_active' => $this->isActive,
        ];

        if ($this->editingCouponId) {
            $coupon = Coupon::findOrFail($this->editingCouponId);
            $coupon->update($payload);
            $this->feedbackMessage = "تم تحديث الكوبون \"{$coupon->code}\" بنجاح.";
        } else {
            Coupon::create($payload);
            $this->feedbackMessage = 'تمت إضافة الكوبون الجديد بنجاح.';
        }

        $this->showModal = false;
        $this->reset(['editingCouponId', 'code', 'minOrder', 'expiresAt', 'maxUses']);
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['editingCouponId', 'code', 'minOrder', 'expiresAt', 'maxUses']);
        $this->resetValidation();
    }

    public function render()
    {
        $query = Coupon::withCount('orders')
            ->latest();

        if (! empty(trim($this->search))) {
            $query->where('code', 'like', '%' . trim($this->search) . '%');
        }

        $coupons = $query->paginate(10);

        return view('livewire.admin.coupon-manager', [
            'coupons' => $coupons,
        ])->layout('layouts.admin', ['title' => 'إدارة الكوبونات']);
    }
}

