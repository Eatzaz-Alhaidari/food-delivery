<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;

class OrderSeeder extends Seeder
{
    /**
     * بذر طلبات تجريبية بحالات مختلفة لاختبار لوحة المطعم وتتبع العميل
     */
    public function run(): void
    {
        $customer = User::where('email', 'customer@sufra.test')->first();
        $restaurantOwner = User::where('email', 'restaurant@sufra.test')->first();

        if (! $customer || ! $restaurantOwner || ! $restaurantOwner->restaurant) {
            return;
        }

        $restaurant = $restaurantOwner->restaurant;
        $address = $customer->addresses()->first();

        if (! $address) {
            $address = $customer->addresses()->create([
                'title' => 'المنزل',
                'district' => 'حطين',
                'street' => 'شارع الأمير تركي بن عبدالعزيز الأول',
                'details' => 'فيلا 14',
            ]);
        }

        $items = $restaurant->menuItems()->take(3)->get();
        if ($items->isEmpty()) {
            return;
        }

        // ١. طلب جديد (قيد الانتظار) لاختبار القبول والرفض من لوحة المطعم
        if ($restaurant->orders()->where('status', OrderStatus::Pending->value)->doesntExist()) {
            $item = $items->first();
            $subtotal = (float) $item->price * 2;
            $deliveryFee = (float) $restaurant->delivery_fee;
            $total = $subtotal + $deliveryFee;

            $pendingOrder = Order::create([
                'user_id' => $customer->id,
                'restaurant_id' => $restaurant->id,
                'address_id' => $address->id,
                'status' => OrderStatus::Pending,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'payment_method' => 'cash',
                'notes' => 'يرجى إرسال شطة وملاعق إضافية مع الطلب.',
            ]);

            OrderItem::create([
                'order_id' => $pendingOrder->id,
                'menu_item_id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => 2,
                'addons_json' => null,
            ]);

            $pendingOrder->statusLogs()->create([
                'from_status' => null,
                'to_status' => OrderStatus::Pending,
                'changed_by_user_id' => $customer->id,
                'comment' => 'تم إنشاء الطلب بنجاح من العميل وبانتظار قبول المطعم.',
            ]);
        }

        // ٢. طلب جاري (قيد التجهيز) لاختبار ترقية الحالة
        if ($restaurant->orders()->where('status', OrderStatus::Preparing->value)->doesntExist()) {
            $item = $items->get(1) ?? $items->first();
            $subtotal = (float) $item->price;
            $deliveryFee = (float) $restaurant->delivery_fee;
            $total = $subtotal + $deliveryFee;

            $preparingOrder = Order::create([
                'user_id' => $customer->id,
                'restaurant_id' => $restaurant->id,
                'address_id' => $address->id,
                'status' => OrderStatus::Preparing,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'total' => $total,
                'payment_method' => 'card_mock',
                'notes' => null,
            ]);

            OrderItem::create([
                'order_id' => $preparingOrder->id,
                'menu_item_id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => 1,
                'addons_json' => null,
            ]);

            $preparingOrder->statusLogs()->create([
                'from_status' => null,
                'to_status' => OrderStatus::Pending,
                'changed_by_user_id' => $customer->id,
                'comment' => 'تم استلام الطلب.',
            ]);

            $preparingOrder->statusLogs()->create([
                'from_status' => OrderStatus::Pending,
                'to_status' => OrderStatus::Accepted,
                'changed_by_user_id' => $restaurantOwner->id,
                'comment' => 'تم قبول الطلب وجاري إرساله للمطبخ.',
            ]);

            $preparingOrder->statusLogs()->create([
                'from_status' => OrderStatus::Accepted,
                'to_status' => OrderStatus::Preparing,
                'changed_by_user_id' => $restaurantOwner->id,
                'comment' => 'بدأ الشيف في إعداد الوجبة.',
            ]);
        }

        // ٣. طلب مكتمل ومسلّم (غير مقيّم حتى الآن) لاختبار نموذج إضافة تقييم جديد في واجهة التتبع (المرحلة 8)
        if ($restaurant->orders()->where('status', OrderStatus::Delivered->value)->whereDoesntHave('review')->doesntExist()) {
            $item = $items->last() ?? $items->first();
            $subtotal = (float) $item->price * 2;
            $deliveryFee = (float) $restaurant->delivery_fee;
            $total = $subtotal + $deliveryFee;

            $deliveredOrder = Order::create([
                'user_id' => $customer->id,
                'restaurant_id' => $restaurant->id,
                'address_id' => $address->id,
                'status' => OrderStatus::Delivered,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'discount_amount' => 0.00,
                'total' => $total,
                'payment_method' => 'cash',
                'notes' => 'يرجى التوصيل عند الباب.',
            ]);

            OrderItem::create([
                'order_id' => $deliveredOrder->id,
                'menu_item_id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => 2,
                'addons_json' => null,
            ]);

            $deliveredOrder->statusLogs()->create([
                'from_status' => null,
                'to_status' => OrderStatus::Pending,
                'changed_by_user_id' => $customer->id,
                'comment' => 'تم إنشاء الطلب.',
            ]);

            $deliveredOrder->statusLogs()->create([
                'from_status' => OrderStatus::Pending,
                'to_status' => OrderStatus::Delivered,
                'changed_by_user_id' => $restaurantOwner->id,
                'comment' => 'تم تسليم الطلب للعميل بنجاح.',
            ]);
        }
    }
}

