<?php

namespace App\Livewire\Customer;

use App\Models\Restaurant;
use Livewire\Attributes\Url;
use Livewire\Component;

class RestaurantList extends Component
{
    /**
     * متغيرات الفلترة محفوظة في الرابط (Query String)
     */
    #[Url(as: 'q', history: true)]
    public string $search = '';

    #[Url(as: 'cuisine', history: true)]
    public string $cuisine = '';

    #[Url(as: 'open', history: true)]
    public bool $openOnly = false;

    #[Url(as: 'sort', history: true)]
    public string $sortBy = '';

    /**
     * إعادة تعيين جميع الفلاتر
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->cuisine = '';
        $this->openOnly = false;
        $this->sortBy = '';
        $this->dispatch('lucide:refresh');
    }

    /**
     * اختيار نوع مطبخ محدد للفلترة
     */
    public function selectCuisine(string $cuisine): void
    {
        $this->cuisine = ($this->cuisine === $cuisine) ? '' : $cuisine;
        $this->dispatch('lucide:refresh');
    }

    public function render()
    {
        // استعلام المطاعم النشطة فقط مع تطبيق الفلاتر
        $query = Restaurant::active();

        // ١. فلترة البحث بالاسم أو الحي أو المطبخ
        if (!empty(trim($this->search))) {
            $term = trim($this->search);
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('district', 'like', "%{$term}%")
                  ->orWhere('cuisine', 'like', "%{$term}%");
            });
        }

        // ٢. فلترة نوع المطبخ
        if (!empty($this->cuisine)) {
            $query->where('cuisine', $this->cuisine);
        }

        // ٣. فلترة المطاعم المفتوحة الآن
        if ($this->openOnly) {
            $query->open();
        }

        // ٤. الترتيب (الأسرع تحضيراً، أو الأقل رسوماً للتوصيل)
        if ($this->sortBy === 'fastest') {
            $query->orderBy('prep_time', 'asc');
        } elseif ($this->sortBy === 'lowest_fee') {
            $query->orderBy('delivery_fee', 'asc');
        } else {
            // الافتراضي: المطاعم المفتوحة أولاً ثم الترتيب الأبجدي
            $query->orderBy('is_open', 'desc')->orderBy('name', 'asc');
        }

        $restaurants = $query->get();

        // قائمة أنواع المطابخ المتاحة للفلترة
        $availableCuisines = Restaurant::active()
            ->distinct()
            ->pluck('cuisine')
            ->filter()
            ->values();

        return view('livewire.customer.restaurant-list', [
            'restaurants' => $restaurants,
            'availableCuisines' => $availableCuisines,
        ])->layout('layouts.app', ['title' => 'المطاعم']);
    }
}

