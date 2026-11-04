<?php

namespace App\Livewire\Customer;

use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AddressManager extends Component
{
    public bool $showModal = false;
    public ?int $editingAddressId = null;

    public string $title = '';
    public string $district = '';
    public string $street = '';
    public string $details = '';

    public ?string $successMessage = null;

    /**
     * قواعد التحقق برسائل عربية
     */
    protected function rules(): array
    {
        return [
            'title' => 'required|string|min:2|max:60',
            'district' => 'required|string|min:2|max:100',
            'street' => 'required|string|min:2|max:100',
            'details' => 'nullable|string|max:500',
        ];
    }

    protected function messages(): array
    {
        return [
            'title.required' => 'حقل تسمية العنوان (مثل: المنزل، العمل) مطلوب.',
            'title.min' => 'يجب ألا تقل تسمية العنوان عن حرفين.',
            'district.required' => 'حقل اسم الحي مطلوب.',
            'street.required' => 'حقل اسم الشارع مطلوب.',
            'details.max' => 'التفاصيل الإضافية يجب ألا تتجاوز 500 حرف.',
        ];
    }

    public function openCreateModal(): void
    {
        $this->reset(['editingAddressId', 'title', 'district', 'street', 'details', 'successMessage']);
        $this->resetValidation();
        $this->showModal = true;
    }

    public function editAddress(int $id): void
    {
        $address = Auth::user()->addresses()->findOrFail($id);

        $this->editingAddressId = $address->id;
        $this->title = $address->title;
        $this->district = $address->district;
        $this->street = $address->street;
        $this->details = $address->details ?? '';

        $this->resetValidation();
        $this->showModal = true;
    }

    public function saveAddress(): void
    {
        $validated = $this->validate();

        if ($this->editingAddressId) {
            $address = Auth::user()->addresses()->findOrFail($this->editingAddressId);
            $address->update($validated);
            $this->successMessage = 'تم تحديث العنوان بنجاح.';
        } else {
            Auth::user()->addresses()->create($validated);
            $this->successMessage = 'تمت إضافة العنوان الجديد بنجاح.';
        }

        $this->showModal = false;
        $this->reset(['editingAddressId', 'title', 'district', 'street', 'details']);
    }

    public function deleteAddress(int $id): void
    {
        $address = Auth::user()->addresses()->findOrFail($id);
        $address->delete();
        $this->successMessage = 'تم حذف العنوان.';
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->reset(['editingAddressId', 'title', 'district', 'street', 'details']);
        $this->resetValidation();
    }

    public function render()
    {
        $addresses = Auth::user()->addresses()->latest()->get();

        return view('livewire.customer.address-manager', [
            'addresses' => $addresses,
        ])->layout('layouts.app', ['title' => 'عناوين التوصيل']);
    }
}

