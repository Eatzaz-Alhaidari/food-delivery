<?php

namespace App\Livewire\Admin;

use App\Models\PlatformSetting;
use Livewire\Component;

class SettingsManager extends Component
{
    public float $commissionPercentage = 10.0;
    public ?string $feedbackMessage = null;

    public function mount(): void
    {
        $this->commissionPercentage = PlatformSetting::getCommissionPercentage();
    }

    public function saveSettings(): void
    {
        $validated = $this->validate([
            'commissionPercentage' => 'required|numeric|min:0|max:100',
        ], [
            'commissionPercentage.required' => 'حقل نسبة عمولة المنصة مطلوب.',
            'commissionPercentage.numeric' => 'نسبة العمولة يجب أن تكون رقماً.',
            'commissionPercentage.min' => 'نسبة العمولة لا يمكن أن تقل عن 0%.',
            'commissionPercentage.max' => 'نسبة العمولة لا يمكن أن تزيد عن 100%.',
        ]);

        PlatformSetting::set(
            'commission_percentage',
            $validated['commissionPercentage'],
            'float',
            'نسبة عمولة المنصة الافتراضية من مبيعات المطاعم'
        );

        $this->feedbackMessage = 'تم حفظ إعدادات عمولة المنصة بنجاح.';
    }

    public function render()
    {
        return view('livewire.admin.settings-manager')
            ->layout('layouts.admin', ['title' => 'إعدادات المنصة']);
    }
}

