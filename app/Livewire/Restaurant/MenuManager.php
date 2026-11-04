<?php

namespace App\Livewire\Restaurant;

use App\Models\Addon;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithFileUploads;

class MenuManager extends Component
{
    use AuthorizesRequests, WithFileUploads;

    public Restaurant $restaurant;

    // التصنيف المحدد حالياً للعرض ('all' أو category_id)
    public $selectedCategoryId = 'all';

    // حالة نافذة التصنيف
    public bool $showCategoryModal = false;
    public ?int $editingCategoryId = null;
    public string $categoryName = '';
    public int $categorySortOrder = 0;

    // حالة نافذة الصنف
    public bool $showItemModal = false;
    public ?int $editingItemId = null;
    public ?int $itemCategoryId = null;
    public string $itemName = '';
    public ?string $itemDescription = '';
    public $itemPrice = '';
    public $itemImage;
    public bool $itemIsAvailable = true;

    // قائمة الإضافات للصنف المفتوح حالياً
    public array $itemAddons = [];
    public string $newAddonName = '';
    public $newAddonPrice = '';

    public ?string $successMessage = null;
    public ?string $errorMessage = null;

    /**
     * قواعد التحقق لإضافة وتعديل التصنيف
     */
    protected function categoryRules(): array
    {
        return [
            'categoryName' => ['required', 'string', 'max:100'],
            'categorySortOrder' => ['required', 'integer', 'min:0', 'max:999'],
        ];
    }

    /**
     * قواعد التحقق لإضافة وتعديل الصنف
     */
    protected function itemRules(): array
    {
        return [
            'itemCategoryId' => ['required', 'exists:categories,id'],
            'itemName' => ['required', 'string', 'max:255'],
            'itemDescription' => ['nullable', 'string', 'max:1000'],
            'itemPrice' => ['required', 'numeric', 'min:0.5', 'max:5000'],
            'itemImage' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,webp'],
            'itemIsAvailable' => ['boolean'],
        ];
    }

    /**
     * تهيئة المكون والتحقق من الصلاحية
     */
    public function mount(): void
    {
        $user = auth()->user();
        $this->restaurant = $user->restaurant ?? abort(403, 'لا يوجد مطعم مرتبط بحسابك.');

        // التحقق من الصلاحية عبر Policy
        $this->authorize('manageMenu', $this->restaurant);
    }

    /**
     * فتح نافذة إنشاء تصنيف جديد
     */
    public function openNewCategoryModal(): void
    {
        $this->resetCategoryForm();
        $this->categorySortOrder = ($this->restaurant->categories()->max('sort_order') ?? 0) + 1;
        $this->showCategoryModal = true;
        $this->dispatch('lucide:refresh');
    }

    /**
     * فتح نافذة تعديل تصنيف موجود
     */
    public function editCategory(int $categoryId): void
    {
        $category = Category::findOrFail($categoryId);
        // منع التعديل على تصنيف يتبع لمطعم آخر
        abort_if($category->restaurant_id !== $this->restaurant->id, 403, 'غير مصرح بتعديل هذا التصنيف.');

        $this->editingCategoryId = $category->id;
        $this->categoryName = $category->name;
        $this->categorySortOrder = $category->sort_order;
        $this->showCategoryModal = true;
        $this->dispatch('lucide:refresh');
    }

    /**
     * حفظ التصنيف (إنشاء أو تحديث)
     */
    public function saveCategory(): void
    {
        $this->authorize('manageMenu', $this->restaurant);
        $this->validate($this->categoryRules(), [], [
            'categoryName' => 'اسم التصنيف',
            'categorySortOrder' => 'ترتيب العرض',
        ]);

        if ($this->editingCategoryId) {
            $category = Category::findOrFail($this->editingCategoryId);
            abort_if($category->restaurant_id !== $this->restaurant->id, 403);

            $category->update([
                'name' => $this->categoryName,
                'sort_order' => $this->categorySortOrder,
            ]);
            $this->successMessage = 'تم تحديث التصنيف بنجاح.';
        } else {
            $this->restaurant->categories()->create([
                'name' => $this->categoryName,
                'sort_order' => $this->categorySortOrder,
            ]);
            $this->successMessage = 'تمت إضافة التصنيف الجديد بنجاح.';
        }

        $this->showCategoryModal = false;
        $this->resetCategoryForm();
        $this->dispatch('lucide:refresh');
    }

    /**
     * حذف تصنيف مع كامل أصنافه بعد التحقق
     */
    public function deleteCategory(int $categoryId): void
    {
        $this->authorize('manageMenu', $this->restaurant);
        $category = Category::findOrFail($categoryId);
        abort_if($category->restaurant_id !== $this->restaurant->id, 403);

        $category->delete();

        if ($this->selectedCategoryId == $categoryId) {
            $this->selectedCategoryId = 'all';
        }

        $this->successMessage = 'تم حذف التصنيف وجميع أصنافه بنجاح.';
        $this->dispatch('lucide:refresh');
    }

    /**
     * رفع ترتيب التصنيف للأعلى
     */
    public function moveCategoryUp(int $categoryId): void
    {
        $category = Category::findOrFail($categoryId);
        abort_if($category->restaurant_id !== $this->restaurant->id, 403);

        $prev = $this->restaurant->categories()
            ->where('sort_order', '<', $category->sort_order)
            ->orderBy('sort_order', 'desc')
            ->first();

        if ($prev) {
            $temp = $category->sort_order;
            $category->update(['sort_order' => $prev->sort_order]);
            $prev->update(['sort_order' => $temp]);
        }
        $this->dispatch('lucide:refresh');
    }

    /**
     * خفض ترتيب التصنيف للأسفل
     */
    public function moveCategoryDown(int $categoryId): void
    {
        $category = Category::findOrFail($categoryId);
        abort_if($category->restaurant_id !== $this->restaurant->id, 403);

        $next = $this->restaurant->categories()
            ->where('sort_order', '>', $category->sort_order)
            ->orderBy('sort_order', 'asc')
            ->first();

        if ($next) {
            $temp = $category->sort_order;
            $category->update(['sort_order' => $next->sort_order]);
            $next->update(['sort_order' => $temp]);
        }
        $this->dispatch('lucide:refresh');
    }

    /**
     * تبديل حالة توفر الصنف فورياً
     */
    public function toggleAvailability(int $itemId): void
    {
        $item = MenuItem::with('category')->findOrFail($itemId);
        abort_if($item->category->restaurant_id !== $this->restaurant->id, 403);

        $item->is_available = ! $item->is_available;
        $item->save();

        $this->successMessage = $item->is_available
            ? "تم تعيين الصنف «{$item->name}» كمتوفر للطلب."
            : "تم تعيين الصنف «{$item->name}» كغير متوفر (نفد).";

        $this->dispatch('lucide:refresh');
    }

    /**
     * فتح نافذة إضافة صنف جديد
     */
    public function openNewItemModal(): void
    {
        $this->resetItemForm();
        $firstCategory = $this->restaurant->categories()->first();
        if ($this->selectedCategoryId !== 'all') {
            $this->itemCategoryId = (int) $this->selectedCategoryId;
        } else {
            $this->itemCategoryId = $firstCategory?->id;
        }

        $this->showItemModal = true;
        $this->dispatch('lucide:refresh');
    }

    /**
     * فتح نافذة تعديل صنف
     */
    public function editItem(int $itemId): void
    {
        $item = MenuItem::with(['category', 'addons'])->findOrFail($itemId);
        abort_if($item->category->restaurant_id !== $this->restaurant->id, 403);

        $this->editingItemId = $item->id;
        $this->itemCategoryId = $item->category_id;
        $this->itemName = $item->name;
        $this->itemDescription = $item->description;
        $this->itemPrice = $item->price;
        $this->itemIsAvailable = (bool) $item->is_available;
        $this->itemImage = null;

        $this->itemAddons = $item->addons->map(function ($addon) {
            return [
                'id' => $addon->id,
                'name' => $addon->name,
                'extra_price' => (float) $addon->extra_price,
            ];
        })->toArray();

        $this->showItemModal = true;
        $this->dispatch('lucide:refresh');
    }

    /**
     * إضافة خيار/إضافة لقائمة الإضافات داخل نافذة الصنف
     */
    public function addAddon(): void
    {
        $this->validate([
            'newAddonName' => ['required', 'string', 'max:100'],
            'newAddonPrice' => ['required', 'numeric', 'min:0', 'max:500'],
        ], [], [
            'newAddonName' => 'اسم الإضافة',
            'newAddonPrice' => 'سعر الإضافة',
        ]);

        $this->itemAddons[] = [
            'id' => null,
            'name' => $this->newAddonName,
            'extra_price' => (float) $this->newAddonPrice,
        ];

        $this->newAddonName = '';
        $this->newAddonPrice = '';
        $this->dispatch('lucide:refresh');
    }

    /**
     * حذف إضافة من الصنف
     */
    public function removeAddon(int $index): void
    {
        unset($this->itemAddons[$index]);
        $this->itemAddons = array_values($this->itemAddons);
        $this->dispatch('lucide:refresh');
    }

    /**
     * حفظ الصنف وإضافاته
     */
    public function saveItem(): void
    {
        $this->authorize('manageMenu', $this->restaurant);
        $this->validate($this->itemRules(), [], [
            'itemCategoryId' => 'التصنيف',
            'itemName' => 'اسم الصنف',
            'itemDescription' => 'الوصف',
            'itemPrice' => 'السعر',
            'itemImage' => 'الصورة',
            'itemIsAvailable' => 'حالة التوفر',
        ]);

        // التحقق من أن التصنيف المختار يخص نفس المطعم
        $category = Category::findOrFail($this->itemCategoryId);
        abort_if($category->restaurant_id !== $this->restaurant->id, 403);

        $itemData = [
            'category_id' => $this->itemCategoryId,
            'name' => $this->itemName,
            'description' => $this->itemDescription,
            'price' => $this->itemPrice,
            'is_available' => $this->itemIsAvailable,
        ];

        if ($this->itemImage) {
            $itemData['image'] = $this->itemImage->store('menu_items', 'public');
        }

        if ($this->editingItemId) {
            $item = MenuItem::findOrFail($this->editingItemId);
            abort_if($item->category->restaurant_id !== $this->restaurant->id, 403);

            $item->update($itemData);
            $this->successMessage = 'تم تحديث بيانات الصنف بنجاح.';
        } else {
            $item = MenuItem::create($itemData);
            $this->successMessage = 'تمت إضافة الصنف الجديد بنجاح.';
        }

        // تحديث الإضافات المرتبطة بالصنف
        $existingAddonIds = [];
        foreach ($this->itemAddons as $addonData) {
            if (!empty($addonData['id'])) {
                $addon = Addon::where('menu_item_id', $item->id)->find($addonData['id']);
                if ($addon) {
                    $addon->update([
                        'name' => $addonData['name'],
                        'extra_price' => $addonData['extra_price'],
                    ]);
                    $existingAddonIds[] = $addon->id;
                }
            } else {
                $newAddon = $item->addons()->create([
                    'name' => $addonData['name'],
                    'extra_price' => $addonData['extra_price'],
                ]);
                $existingAddonIds[] = $newAddon->id;
            }
        }

        // حذف الإضافات التي أزالها المستخدم
        $item->addons()->whereNotIn('id', $existingAddonIds)->delete();

        $this->showItemModal = false;
        $this->resetItemForm();
        $this->dispatch('lucide:refresh');
    }

    /**
     * حذف صنف من المنيو
     */
    public function deleteItem(int $itemId): void
    {
        $this->authorize('manageMenu', $this->restaurant);
        $item = MenuItem::with('category')->findOrFail($itemId);
        abort_if($item->category->restaurant_id !== $this->restaurant->id, 403);

        $item->delete();
        $this->successMessage = 'تم حذف الصنف بنجاح.';
        $this->dispatch('lucide:refresh');
    }

    protected function resetCategoryForm(): void
    {
        $this->editingCategoryId = null;
        $this->categoryName = '';
        $this->categorySortOrder = 0;
        $this->resetValidation();
    }

    protected function resetItemForm(): void
    {
        $this->editingItemId = null;
        $this->itemCategoryId = null;
        $this->itemName = '';
        $this->itemDescription = '';
        $this->itemPrice = '';
        $this->itemImage = null;
        $this->itemIsAvailable = true;
        $this->itemAddons = [];
        $this->newAddonName = '';
        $this->newAddonPrice = '';
        $this->resetValidation();
    }

    public function render()
    {
        $categories = $this->restaurant->categories()->withCount('menuItems')->get();

        $itemsQuery = MenuItem::whereHas('category', function ($query) {
            $query->where('restaurant_id', $this->restaurant->id);
        })->with(['category', 'addons']);

        if ($this->selectedCategoryId !== 'all') {
            $itemsQuery->where('category_id', $this->selectedCategoryId);
        }

        $items = $itemsQuery->latest()->get();

        return view('livewire.restaurant.menu-manager', [
            'categories' => $categories,
            'items' => $items,
        ])->layout('layouts.restaurant', ['title' => 'إدارة المنيو']);
    }
}

