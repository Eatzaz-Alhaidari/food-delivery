<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * بذر تقييمات أولية واقعية للمطاعم من طلبات مسلّمة
     */
    public function run(): void
    {
        $customer = User::where('email', 'customer@sufra.test')->first();
        if (! $customer) {
            return;
        }

        $address = $customer->addresses()->first();
        $restaurants = Restaurant::all();

        $sampleReviews = [
            [
                'rating' => 5,
                'comment' => 'تجربة ممتازة جداً، الأكل وصل حاراً وطازجاً والتغليف احترافي ونظيف للغاية. أنصح بالطلب منهم بكل تأكيد!',
            ],
            [
                'rating' => 5,
                'comment' => 'من ألذ وأجود الأطباق التي جربتها، النكهة أصيلة ومضبوطة والتوصيل أسرع من المتوقع.',
            ],
            [
                'rating' => 4,
                'comment' => 'الطعام رائع ولذيذ جداً، فقط تأخر السائق بضع دقائق عن الموعد لكن التجربة بشكل عام تستحق الإشادة.',
            ],
            [
                'rating' => 5,
                'comment' => 'أفضل كبسة في الحي بلا منازع، كميات مشبعة ونكهة غنية ومكونات طازجة.',
            ],
            [
                'rating' => 4,
                'comment' => 'جودة ممتازة وسعر مناسب جداً للكمية والطعم. سأكرر الطلب بإذن الله.',
            ],
        ];

        foreach ($restaurants as $index => $restaurant) {
            // التحقق من وجود تقييم مسبق
            if ($restaurant->reviews()->count() >= 2) {
                continue;
            }

            $items = $restaurant->menuItems()->take(2)->get();
            if ($items->isEmpty()) {
                continue;
            }

            // إنشاء طلب مسلّم لهذا المطعم ومربوط بالعميل
            $subtotal = (float) $items->first()->price;
            $deliveryFee = (float) $restaurant->delivery_fee;

            $deliveredOrder = Order::create([
                'user_id' => $customer->id,
                'restaurant_id' => $restaurant->id,
                'address_id' => $address?->id,
                'status' => OrderStatus::Delivered,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount_amount' => 0.00,
                'total' => $subtotal + $deliveryFee,
                'payment_method' => 'card_mock',
                'notes' => null,
            ]);

            OrderItem::create([
                'order_id' => $deliveredOrder->id,
                'menu_item_id' => $items->first()->id,
                'name' => $items->first()->name,
                'price' => $items->first()->price,
                'quantity' => 1,
                'addons_json' => null,
            ]);

            $sample = $sampleReviews[$index % count($sampleReviews)];

            Review::create([
                'order_id' => $deliveredOrder->id,
                'restaurant_id' => $restaurant->id,
                'user_id' => $customer->id,
                'rating' => $sample['rating'],
                'comment' => $sample['comment'],
            ]);
        }
    }
}

