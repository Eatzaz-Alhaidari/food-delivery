<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Addon;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RestaurantSeeder extends Seeder
{
    /**
     * بذر 8 مطاعم سعودية متكاملة مع تصنيفاتها وأصنافها وإضافاتها
     */
    public function run(): void
    {
        $restaurantsData = [
            [
                'user' => [
                    'name' => 'مطعم شواية نجد',
                    'email' => 'restaurant@sufra.test',
                ],
                'restaurant' => [
                    'name' => 'شواية نجد',
                    'slug' => 'shawaya-najd',
                    'description' => 'أشهى دجاج الشواية مع الأرز البشاور والشعبي على أصوله النجدية.',
                    'cuisine' => 'شعبي',
                    'district' => 'الصحافة',
                    'logo' => 'demo/restaurants/najd-logo.jpg',
                    'cover' => 'demo/restaurants/najd-cover.jpg',
                    'min_order' => 30.00,
                    'delivery_fee' => 10.00,
                    'prep_time' => 25,
                    'is_open' => true,
                    'is_active' => true,
                ],
                'categories' => [
                    [
                        'name' => 'وجبات الشواية والأرز',
                        'sort_order' => 1,
                        'items' => [
                            [
                                'name' => 'نصف حبة شواية مع رز بشاور',
                                'description' => 'دجاج شواية متبل بالبهارات النجدية يقدم مع رز بشاور فاخر.',
                                'price' => 24.00,
                                'image' => 'demo/dishes/shawaya-half-peshawar.jpg',
                                'addons' => [
                                    ['name' => 'رز بشاور إضافي', 'extra_price' => 7.00],
                                    ['name' => 'دقوس حار نجدي', 'extra_price' => 2.00],
                                    ['name' => 'طحينة خاصة', 'extra_price' => 2.50],
                                ],
                            ],
                            [
                                'name' => 'حبة كاملة شواية مع رز شعبي',
                                'description' => 'دجاجة كاملة مقمرة مع رز شعبي أحمر بالبصل المحمر والمكسرات.',
                                'price' => 46.00,
                                'image' => 'demo/dishes/shawaya-full-shabi.jpg',
                                'addons' => [
                                    ['name' => 'رز شعبي إضافي', 'extra_price' => 7.00],
                                    ['name' => 'سلطة خضراء طازجة', 'extra_price' => 4.00],
                                ],
                            ],
                            [
                                'name' => 'مضغوط دجاج نجد',
                                'description' => 'دجاج طازج مطبوخ بقدر الضغط مع طماطم وبهارات نجدية كلاسيكية.',
                                'price' => 28.00,
                                'image' => 'demo/dishes/madghoot-chicken.jpg',
                                'addons' => [
                                    ['name' => 'شطة حمراء معبوج', 'extra_price' => 3.00],
                                ],
                            ],
                            [
                                'name' => 'شواية سادة بدون رز',
                                'description' => 'حبة دجاج شواية مقرمشة سادة مع الخبز والمخلل والصوص.',
                                'price' => 34.00,
                                'image' => 'demo/dishes/shawaya-plain.jpg',
                                'addons' => [
                                    ['name' => 'خبز تنور إضافي (٣ حبات)', 'extra_price' => 2.00],
                                    ['name' => 'طحينة وليمون', 'extra_price' => 2.00],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'الإيدامات والمقبلات',
                        'sort_order' => 2,
                        'items' => [
                            [
                                'name' => 'إيدام مشكل خضار',
                                'description' => 'خضار موسمية طازجة مطبوخة بمرق غني ومسبك.',
                                'price' => 12.00,
                                'image' => 'demo/dishes/edam-vegetables.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'ملوخية بالثوم والكزبرة',
                                'description' => 'ملوخية خضراء مفرومة بطشة الثوم المقرمشة وزيت الزيتون.',
                                'price' => 14.00,
                                'image' => 'demo/dishes/molokhia.jpg',
                                'addons' => [
                                    ['name' => 'طشة ثوم مضاعفة', 'extra_price' => 2.00],
                                ],
                            ],
                            [
                                'name' => 'مسقعة باذنجان ولحم مفروم',
                                'description' => 'طبقات باذنجان مشوي مع لحمة مفرومة متبلة وصلصة حمراء.',
                                'price' => 16.00,
                                'image' => 'demo/dishes/moussaka.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'سلطة لبن بالخيار والنعناع',
                                'description' => 'زبادي بارد مع خيار مقرمش ورشة نعناع مجفف.',
                                'price' => 6.00,
                                'image' => 'demo/dishes/yogurt-cucumber.jpg',
                                'addons' => [],
                            ],
                        ],
                    ],
                    [
                        'name' => 'المشروبات والحلويات',
                        'sort_order' => 3,
                        'items' => [
                            [
                                'name' => 'لبن القرية طازج (كبير)',
                                'description' => 'لبن رائب تقليدي بارد ومنعش.',
                                'price' => 5.00,
                                'image' => 'demo/dishes/laban-fresh.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'كنافة بالقشطة البلدية',
                                'description' => 'كنافة ذهبية محشوة قشطة طازجة ومسقية بشيرة خفيفة.',
                                'price' => 18.00,
                                'image' => 'demo/dishes/kunafa-cream.jpg',
                                'addons' => [
                                    ['name' => 'فستق حلبي إضافي', 'extra_price' => 4.00],
                                ],
                            ],
                            [
                                'name' => 'كريم كراميل منزلي',
                                'description' => 'حلى بارد غني بكراميل السكر المكرمل.',
                                'price' => 9.00,
                                'image' => 'demo/dishes/creme-caramel.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'مياه نقيّة (٣٣٠ مل)',
                                'description' => 'مياه شرب معبأة مبردة.',
                                'price' => 1.50,
                                'image' => 'demo/dishes/water.jpg',
                                'addons' => [],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'user' => [
                    'name' => 'مطبخ قرية التراث',
                    'email' => 'turath@sufra.test',
                ],
                'restaurant' => [
                    'name' => 'قرية التراث للمندي',
                    'slug' => 'qaryat-alturath',
                    'description' => 'مندي اللحم الحنيذ والمظبي بحفرة الفحم الطبيعي وحطب السمر.',
                    'cuisine' => 'مندي ومظبي',
                    'district' => 'الملقا',
                    'logo' => 'demo/restaurants/turath-logo.jpg',
                    'cover' => 'demo/restaurants/turath-cover.jpg',
                    'min_order' => 50.00,
                    'delivery_fee' => 15.00,
                    'prep_time' => 40,
                    'is_open' => true,
                    'is_active' => true,
                ],
                'categories' => [
                    [
                        'name' => 'لحوم المندي والحنيذ',
                        'sort_order' => 1,
                        'items' => [
                            [
                                'name' => 'نفر مندي لحم بلدي',
                                'description' => 'لحم تيس بلدي طازج مطهو في برميل المندي مع رز عنبر مدخن.',
                                'price' => 78.00,
                                'image' => 'demo/dishes/mandi-meat-single.jpg',
                                'addons' => [
                                    ['name' => 'مكسرات محمصة وكبدة', 'extra_price' => 9.00],
                                    ['name' => 'رز عنبر إضافي', 'extra_price' => 8.00],
                                ],
                            ],
                            [
                                'name' => 'نفر حنيذ بالمرخ',
                                'description' => 'لحم حنيذ طري مبخر بأعواد شجر المرخ الجبلي الجنوبي.',
                                'price' => 82.00,
                                'image' => 'demo/dishes/haneeth-markh.jpg',
                                'addons' => [
                                    ['name' => 'عسل سدر طبيعي جانبي', 'extra_price' => 6.00],
                                ],
                            ],
                            [
                                'name' => 'مظبي لحم على الحصى',
                                'description' => 'لحم مشوي على أحجار المرو النظيفة بملح بحري خشن.',
                                'price' => 75.00,
                                'image' => 'demo/dishes/madhbi-meat.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'نصف تيس مندي ملكي (مناسبات)',
                                'description' => 'نصف ذبيحة تيس كاملة مع صينية رز كبيرة وسلطات مشكلة.',
                                'price' => 580.00,
                                'image' => 'demo/dishes/mandi-half-goat.jpg',
                                'addons' => [
                                    ['name' => 'كبدة وكلاوي إضافية', 'extra_price' => 35.00],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'دجاج المندي والمظبي',
                        'sort_order' => 2,
                        'items' => [
                            [
                                'name' => 'نصف حبة مندي دجاج',
                                'description' => 'دجاج طري مطهو على هداوة الفحم مع رز المندي الأصفر.',
                                'price' => 23.00,
                                'image' => 'demo/dishes/mandi-chicken-half.jpg',
                                'addons' => [
                                    ['name' => 'دقوس حار إضافي', 'extra_price' => 2.00],
                                ],
                            ],
                            [
                                'name' => 'حبة دجاج مظبي على الجمر',
                                'description' => 'دجاجة مفتوحة ومشوية ببطء على الحصى الحار.',
                                'price' => 44.00,
                                'image' => 'demo/dishes/madhbi-chicken-full.jpg',
                                'addons' => [
                                    ['name' => 'طحينة حارة بالليمون', 'extra_price' => 3.00],
                                ],
                            ],
                            [
                                'name' => 'مدفون دجاج بالقصدير',
                                'description' => 'دجاج متبل بالبصل والبهارات ومكمور داخل الجمر.',
                                'price' => 25.00,
                                'image' => 'demo/dishes/madfoon-chicken.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'رز مندي سادة بصحن كبير',
                                'description' => 'أرز حبة طويلة مع بهارات المندي الصحيحة والزبيب.',
                                'price' => 12.00,
                                'image' => 'demo/dishes/mandi-rice-plain.jpg',
                                'addons' => [],
                            ],
                        ],
                    ],
                    [
                        'name' => 'المقبلات والمشروبات الشعبية',
                        'sort_order' => 3,
                        'items' => [
                            [
                                'name' => 'سلطة سحاوق بالجبن البلدي',
                                'description' => 'طماطم وفلفل حار وثوم مطحونة مع قطع جبن مالح.',
                                'price' => 7.00,
                                'image' => 'demo/dishes/sahawiq-cheese.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'شوربة مقادم دسمة',
                                'description' => 'شوربة كوارع مطبوخة لعدة ساعات بالهيل والمستكة.',
                                'price' => 20.00,
                                'image' => 'demo/dishes/soup-kaware.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'مرق لحم صافي',
                                'description' => 'كوب من خلاصة مرقة اللحم بالبهارات الخفيفة.',
                                'price' => 4.00,
                                'image' => 'demo/dishes/meat-broth.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'عصير رمان طبيعي',
                                'description' => 'عصير رمان طازج معصور بدون سكر إضافي.',
                                'price' => 14.00,
                                'image' => 'demo/dishes/pomegranate-juice.jpg',
                                'addons' => [],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'user' => [
                    'name' => 'فطور ومطبق الخالدية',
                    'email' => 'khaldiah@sufra.test',
                ],
                'restaurant' => [
                    'name' => 'مطبق ومعصوب الخالدية',
                    'slug' => 'khalidiah-breakfast',
                    'description' => 'فطور حجازي وشعبي: معصوب ملكي، مطبق مالح وحلو، وفول جرة مبخر بالسمن.',
                    'cuisine' => 'فطور شعبي',
                    'district' => 'العليا',
                    'logo' => 'demo/restaurants/khaldiah-logo.jpg',
                    'cover' => 'demo/restaurants/khaldiah-cover.jpg',
                    'min_order' => 20.00,
                    'delivery_fee' => 9.00,
                    'prep_time' => 20,
                    'is_open' => true,
                    'is_active' => true,
                ],
                'categories' => [
                    [
                        'name' => 'المطبق الحجازي',
                        'sort_order' => 1,
                        'items' => [
                            [
                                'name' => 'مطبق مالح لحم بالكرات',
                                'description' => 'عجينة رقيقة محشوة لحم مفروم وكرات وبيض وتوابل خاصة.',
                                'price' => 14.00,
                                'image' => 'demo/dishes/mutabbaq-meat.jpg',
                                'addons' => [
                                    ['name' => 'ليمون وفلفل أخضر حار إضافي', 'extra_price' => 1.50],
                                    ['name' => 'جبن شيدر مبشور بالداخل', 'extra_price' => 3.00],
                                ],
                            ],
                            [
                                'name' => 'مطبق جبن سائل شيدر',
                                'description' => 'مطبق مقرمش محشو بجبن الشيدر الكثيف المذاب.',
                                'price' => 15.00,
                                'image' => 'demo/dishes/mutabbaq-cheese.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'مطبق حلو بالموز والقشطة',
                                'description' => 'مطبق حلو محشو شرائح موز وقشطة بلدية وسكر ناعم.',
                                'price' => 13.00,
                                'image' => 'demo/dishes/mutabbaq-sweet-banana.jpg',
                                'addons' => [
                                    ['name' => 'عسل سدر إضافي', 'extra_price' => 3.00],
                                ],
                            ],
                            [
                                'name' => 'مطبق خضار نباتي',
                                'description' => 'مطبق خفيف بالكرات والبصل الأخضر والطماطم والبيض.',
                                'price' => 11.00,
                                'image' => 'demo/dishes/mutabbaq-veg.jpg',
                                'addons' => [],
                            ],
                        ],
                    ],
                    [
                        'name' => 'المعصوب والعريكة',
                        'sort_order' => 2,
                        'items' => [
                            [
                                'name' => 'معصوب ملكي بالقشطة والعسل',
                                'description' => 'فطير بر مفروم مع موز طازج وقشطة بلدي وعسل وحبة سوداء.',
                                'price' => 22.00,
                                'image' => 'demo/dishes/masoub-royal.jpg',
                                'addons' => [
                                    ['name' => 'جبن شيدر مبشور على الوجه', 'extra_price' => 3.50],
                                    ['name' => 'مكسرات كاجو ولوز محمص', 'extra_price' => 5.00],
                                ],
                            ],
                            [
                                'name' => 'عريكة جنوبية بالسمن والعسل',
                                'description' => 'عجينة بر جنوبية لينة مغطاة بسمن بري بلدي وتمر وعسل سدر.',
                                'price' => 25.00,
                                'image' => 'demo/dishes/areeka-south.jpg',
                                'addons' => [
                                    ['name' => 'قشطة بلدي إضافية', 'extra_price' => 4.00],
                                ],
                            ],
                            [
                                'name' => 'معصوب قشطة عادي',
                                'description' => 'معصوب الموز الكلاسيكي مع القشطة ورشة سمن خفيف.',
                                'price' => 16.00,
                                'image' => 'demo/dishes/masoub-cream.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'فتة تمر بالزبدة',
                                'description' => 'فتة خبز صاج بر معجون بخلاص تمر الإحساء والزبدة الفاخرة.',
                                'price' => 18.00,
                                'image' => 'demo/dishes/fatteh-dates.jpg',
                                'addons' => [],
                            ],
                        ],
                    ],
                    [
                        'name' => 'الفول والقلايات',
                        'sort_order' => 3,
                        'items' => [
                            [
                                'name' => 'فول جرة مبخر بالسمن البري',
                                'description' => 'فول مطبوخ على الجمر ومبخر بفحمة وزيت سمسم وسمن بري.',
                                'price' => 10.00,
                                'image' => 'demo/dishes/foul-jarra.jpg',
                                'addons' => [
                                    ['name' => 'طحينة إضافية', 'extra_price' => 2.00],
                                    ['name' => 'سمن بري مضاعف', 'extra_price' => 3.00],
                                ],
                            ],
                            [
                                'name' => 'شكشوكة عدني بالجبن',
                                'description' => 'بيض مطبوخ مع بصل وطماطم وفلفل وجبن كيري كريمي.',
                                'price' => 15.00,
                                'image' => 'demo/dishes/shakshuka-adani.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'كبدة غنم طازجة صاج',
                                'description' => 'قطع كبدة غنم بلدية طازجة مع البصل والفلفل الرومي.',
                                'price' => 26.00,
                                'image' => 'demo/dishes/liver-lamb.jpg',
                                'addons' => [
                                    ['name' => 'جبن سائل على الوجه', 'extra_price' => 3.00],
                                ],
                            ],
                            [
                                'name' => 'شاي كرك بالزعفران والهيل',
                                'description' => 'شاي بالحليب المغلي والمبهر بالزعفران النقي.',
                                'price' => 6.00,
                                'image' => 'demo/dishes/karak-tea.jpg',
                                'addons' => [],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'user' => [
                    'name' => 'سفرة أمي نورة',
                    'email' => 'om-noura@sufra.test',
                ],
                'restaurant' => [
                    'name' => 'سفرة أمي نورة',
                    'slug' => 'sufra-om-noura',
                    'description' => 'أكلات بيت نجدية مطبوخة بحب: جريش أبيض، قرصان باللحم، مرقوق، وكبيبة.',
                    'cuisine' => 'أكلات بيت نجدية',
                    'district' => 'النرجس',
                    'logo' => 'demo/restaurants/om-noura-logo.jpg',
                    'cover' => 'demo/restaurants/om-noura-cover.jpg',
                    'min_order' => 35.00,
                    'delivery_fee' => 12.00,
                    'prep_time' => 35,
                    'is_open' => true,
                    'is_active' => true,
                ],
                'categories' => [
                    [
                        'name' => 'الأكلات النجدية التراثية',
                        'sort_order' => 1,
                        'items' => [
                            [
                                'name' => 'جريش أمي نورة الأبيض',
                                'description' => 'قمح مجروش مطبوخ بالحليب واللبن ومسقّى بمسمنة البصل والليمون الأسود.',
                                'price' => 28.00,
                                'image' => 'demo/dishes/jareesh-white.jpg',
                                'addons' => [
                                    ['name' => 'مسمنة نجدية إضافية', 'extra_price' => 3.00],
                                    ['name' => 'سمن بري طبيعي', 'extra_price' => 4.00],
                                ],
                            ],
                            [
                                'name' => 'قرصان باللحم والخضار',
                                'description' => 'رقائق خبز القرصان المشربة بمرق اللحم والقرع والكوسا واللوبيا.',
                                'price' => 32.00,
                                'image' => 'demo/dishes/qursan-meat.jpg',
                                'addons' => [
                                    ['name' => 'بصل محمر ومسمنة إضافية', 'extra_price' => 2.50],
                                ],
                            ],
                            [
                                'name' => 'مرقوق لحم بلدي',
                                'description' => 'عجينة بر مطبوخة مع مرق اللحم والباذنجان والقرع حتى تسبكت.',
                                'price' => 34.00,
                                'image' => 'demo/dishes/marqooq-meat.jpg',
                                'addons' => [
                                    ['name' => 'فلفل حار حساوي', 'extra_price' => 2.00],
                                ],
                            ],
                            [
                                'name' => 'جريش أحمر بالطماطم واللحم',
                                'description' => 'جريش مطبوخ بلحم الغنم والصلصة الحمراء الغنية.',
                                'price' => 30.00,
                                'image' => 'demo/dishes/jareesh-red.jpg',
                                'addons' => [],
                            ],
                        ],
                    ],
                    [
                        'name' => 'المقلقل والإيدامات النجدية',
                        'sort_order' => 2,
                        'items' => [
                            [
                                'name' => 'مقلقل لحم حاشي بالبصل',
                                'description' => 'لحم حاشي صغير مقطع ومحمس مع البصل والكمون والفلفل الأسود.',
                                'price' => 38.00,
                                'image' => 'demo/dishes/mqalqal-hashi.jpg',
                                'addons' => [
                                    ['name' => 'خبز صاج حار (رغيفين)', 'extra_price' => 2.00],
                                ],
                            ],
                            [
                                'name' => 'كبيبة حائل باللحم (٦ حبات)',
                                'description' => 'ورق عنب محشو بالأرز ولحم الغنم والبهار الحائلي الحار.',
                                'price' => 24.00,
                                'image' => 'demo/dishes/kubeiba-hail.jpg',
                                'addons' => [
                                    ['name' => 'دبس رمان إضافي', 'extra_price' => 3.00],
                                ],
                            ],
                            [
                                'name' => 'بادنجانية نجدية',
                                'description' => 'إيدام باذنجان بقطع اللحم والصلصة النجدية الحامضة قليلاً.',
                                'price' => 22.00,
                                'image' => 'demo/dishes/badinjania.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'سلطة نجدية بالبصل والسماق',
                                'description' => 'طماطم، خيار، بصل مقطع رفيع، ليمون، وسماق بلدي.',
                                'price' => 8.00,
                                'image' => 'demo/dishes/salad-najdi.jpg',
                                'addons' => [],
                            ],
                        ],
                    ],
                    [
                        'name' => 'الحلويات الشعبية',
                        'sort_order' => 3,
                        'items' => [
                            [
                                'name' => 'حنيني نجدي بالزبدة',
                                'description' => 'تمر خلاص نجد معجون بخبز البر والزبدة والفلفل الأسود والزنجبيل.',
                                'price' => 22.00,
                                'image' => 'demo/dishes/haneeni.jpg',
                                'addons' => [
                                    ['name' => 'قشطة طازجة جانبية', 'extra_price' => 3.50],
                                ],
                            ],
                            [
                                'name' => 'محلى التمر بالهيل',
                                'description' => 'عصيدة تمر دافئة بنكهة الهيل والزعفران والسمن.',
                                'price' => 20.00,
                                'image' => 'demo/dishes/mhalla-dates.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'كليجا القصيم الأصلية (٤ حبات)',
                                'description' => 'أقراص كليجا مقرمشة محشوة بدبس التمر والزنجبيل والقرفة.',
                                'price' => 16.00,
                                'image' => 'demo/dishes/kleija-qassim.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'دلة قهوة سعودية شقراء',
                                'description' => 'قهوة هرري محمصة خفيفة بالهيل والزعفران والمسمار.',
                                'price' => 25.00,
                                'image' => 'demo/dishes/saudi-coffee-dallah.jpg',
                                'addons' => [],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'user' => [
                    'name' => 'بخاري الحمراء القديم',
                    'email' => 'hamra-bukhari@sufra.test',
                ],
                'restaurant' => [
                    'name' => 'بخاري الحمراء',
                    'slug' => 'hamra-bukhari',
                    'description' => 'أرز البخاري العريق بالجزر والزبيب وحمص الطبيخ مع الدجاج المشوي والسمبوسة.',
                    'cuisine' => 'بخاري',
                    'district' => 'الياسمين',
                    'logo' => 'demo/restaurants/bukhari-logo.jpg',
                    'cover' => 'demo/restaurants/bukhari-cover.jpg',
                    'min_order' => 25.00,
                    'delivery_fee' => 10.00,
                    'prep_time' => 25,
                    'is_open' => true,
                    'is_active' => true,
                ],
                'categories' => [
                    [
                        'name' => 'وجبات البخاري',
                        'sort_order' => 1,
                        'items' => [
                            [
                                'name' => 'نصف حبة دجاج فحم مع رز بخاري',
                                'description' => 'دجاج مشوي على الفحم يقدم مع رز بخاري غني بالجزر المحمر والحمص.',
                                'price' => 24.00,
                                'image' => 'demo/dishes/bukhari-coal-chicken.jpg',
                                'addons' => [
                                    ['name' => 'رز بخاري إضافي', 'extra_price' => 6.00],
                                    ['name' => 'طماطم حارة حامضة (شطة بخاري)', 'extra_price' => 2.00],
                                ],
                            ],
                            [
                                'name' => 'حبة دجاج شواية مع رز بخاري',
                                'description' => 'دجاجة شواية كاملة محمرة مع صحن كبير من الرز البخاري العطري.',
                                'price' => 45.00,
                                'image' => 'demo/dishes/bukhari-full-shawaya.jpg',
                                'addons' => [
                                    ['name' => 'جزر وزبيب إضافي على الوجه', 'extra_price' => 3.00],
                                ],
                            ],
                            [
                                'name' => 'نفر كباب لحم مع رز بخاري',
                                'description' => 'سيخين كباب متبل مشوي على الجمر مع الرز وسلطة الخيار.',
                                'price' => 36.00,
                                'image' => 'demo/dishes/bukhari-kebab.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'رز بخاري سادة',
                                'description' => 'أرز بخاري تقليدي مطبوخ بدهن اللحم الطبيعي مع الجزر والكمون.',
                                'price' => 10.00,
                                'image' => 'demo/dishes/bukhari-rice-plain.jpg',
                                'addons' => [],
                            ],
                        ],
                    ],
                    [
                        'name' => 'المقبلات والسمبوسك البخاري',
                        'sort_order' => 2,
                        'items' => [
                            [
                                'name' => 'منتو بخاري باللحم (٦ حبات)',
                                'description' => 'عجينة المنتو المطهوة على البخار محشوة باللحم والبصل والفلفل الأسود.',
                                'price' => 18.00,
                                'image' => 'demo/dishes/mantu-meat.jpg',
                                'addons' => [
                                    ['name' => 'صلصة شطة خل بخارية', 'extra_price' => 2.00],
                                ],
                            ],
                            [
                                'name' => 'فرموزا مقرمشة بالفرن (٦ حبات)',
                                'description' => 'فطائر مثلثة مقرمشة من الخارج ومحشوة بلحم وبصل مفروم.',
                                'price' => 16.00,
                                'image' => 'demo/dishes/yaghmush-farmouza.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'سمبوسة لحم مقلية (٤ حبات)',
                                'description' => 'سمبوسة مثلثة مقرمشة بحشوة اللحم المعصج بالبصل والكزبرة.',
                                'price' => 10.00,
                                'image' => 'demo/dishes/sambusa-meat.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'سلطة طماطم مطحونة بالكمون والليمون',
                                'description' => 'دقوس الطماطم البخاري الشهير المرافق للرز.',
                                'price' => 3.00,
                                'image' => 'demo/dishes/tomato-daqqous.jpg',
                                'addons' => [],
                            ],
                        ],
                    ],
                    [
                        'name' => 'المشروبات والمرطبات',
                        'sort_order' => 3,
                        'items' => [
                            [
                                'name' => 'لبن رائب مثلج',
                                'description' => 'كوب لبن بارد مملح قليلاً ومنعش بعد الرز البخاري.',
                                'price' => 4.00,
                                'image' => 'demo/dishes/laban-iced.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'مشروب غازي حمضيات (٣٣٠ مل)',
                                'description' => 'مشروب غازي مثلج.',
                                'price' => 4.00,
                                'image' => 'demo/dishes/soft-drink.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'شاي أخضر بخاري بالنعناع',
                                'description' => 'براد شاي أخضر خفيف مريح للهضم بعد الوجبة.',
                                'price' => 8.00,
                                'image' => 'demo/dishes/green-tea.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'أم علي بالمكسرات والقشطة',
                                'description' => 'رقائق بف بيستري بالحليب المكثف والمكسرات الوفيرة.',
                                'price' => 16.00,
                                'image' => 'demo/dishes/om-ali.jpg',
                                'addons' => [],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'user' => [
                    'name' => 'شاورما المعلم',
                    'email' => 'muallem-shawarma@sufra.test',
                ],
                'restaurant' => [
                    'name' => 'شاورما وفطاير المعلم',
                    'slug' => 'al-muallem-shawarma',
                    'description' => 'شاورما لحم غنم نعيمي ودجاج مع الثومية الأصلية، وخبز صاج مخبوز طازجاً.',
                    'cuisine' => 'شاورما ومشويات',
                    'district' => 'السليمانية',
                    'logo' => 'demo/restaurants/muallem-logo.jpg',
                    'cover' => 'demo/restaurants/muallem-cover.jpg',
                    'min_order' => 20.00,
                    'delivery_fee' => 8.00,
                    'prep_time' => 15,
                    'is_open' => true,
                    'is_active' => true,
                ],
                'categories' => [
                    [
                        'name' => 'سندوتشات الشاورما',
                        'sort_order' => 1,
                        'items' => [
                            [
                                'name' => 'شاورما دجاج صاج كلاسيك',
                                'description' => 'شاورما دجاج متبلة بالثوم والمخلل والبطاطس بخبز صاج خفيف.',
                                'price' => 9.00,
                                'image' => 'demo/dishes/shawarma-chicken-saj.jpg',
                                'addons' => [
                                    ['name' => 'ثومية إضافية', 'extra_price' => 2.00],
                                    ['name' => 'شطة حارة', 'extra_price' => 1.00],
                                    ['name' => 'جبن سائل داخل السندوتش', 'extra_price' => 2.50],
                                ],
                            ],
                            [
                                'name' => 'شاورما لحم نعيمي بالبقدونس والطحينة',
                                'description' => 'لحم غنم طازج مع شرائح طماطم وبصل وسماق وصلصة طحينة فاخرة.',
                                'price' => 13.00,
                                'image' => 'demo/dishes/shawarma-meat.jpg',
                                'addons' => [
                                    ['name' => 'طحينة إضافية', 'extra_price' => 2.00],
                                    ['name' => 'دبس رمان', 'extra_price' => 2.00],
                                ],
                            ],
                            [
                                'name' => 'شاورما عربي دجاج (صحن مقطع)',
                                'description' => 'رول شاورما مقطع مع بطاطس مقلية وثومية ومخلل وصوص خاص.',
                                'price' => 22.00,
                                'image' => 'demo/dishes/shawarma-arabi-chicken.jpg',
                                'addons' => [
                                    ['name' => 'بطاطس مقلية زيادة', 'extra_price' => 5.00],
                                ],
                            ],
                            [
                                'name' => 'صاروخ شاورما دجاج حجم عائلي',
                                'description' => 'سندوتش شاورما كبير مضاعف الحجم بخبز الصاج المحمص.',
                                'price' => 17.00,
                                'image' => 'demo/dishes/shawarma-sarookh.jpg',
                                'addons' => [
                                    ['name' => 'جبن موزاريلا مذاب', 'extra_price' => 3.50],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'صحون الشاورما والمشويات',
                        'sort_order' => 2,
                        'items' => [
                            [
                                'name' => 'صحن شاورما دجاج فرط',
                                'description' => 'قطع شاورما بدون خبز مع صحن بطاطس وثومية وخبز صاج جانبي.',
                                'price' => 28.00,
                                'image' => 'demo/dishes/shawarma-plate-chicken.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'صحن شاورما لحم غنم',
                                'description' => 'شاورما لحم صافية مع طحينة وخضار وبصل وسماق.',
                                'price' => 36.00,
                                'image' => 'demo/dishes/shawarma-plate-meat.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'كبة شامية مقلية باللحم والصنوبر (٤ حبات)',
                                'description' => 'أقراص كبة مقرمشة محشوة باللحم المفروم والصنوبر المحمص.',
                                'price' => 16.00,
                                'image' => 'demo/dishes/kibbeh-fried.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'بطاطس ودجز بالبهارات',
                                'description' => 'أصابع بطاطس متبلة ومقلية مقرمشة مع صوص الثوم والكاتشب.',
                                'price' => 9.00,
                                'image' => 'demo/dishes/potato-wedges.jpg',
                                'addons' => [
                                    ['name' => 'صوص جبن شيدر للتغميس', 'extra_price' => 3.00],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'المشروبات والصوصات',
                        'sort_order' => 3,
                        'items' => [
                            [
                                'name' => 'عصير برتقال طازج',
                                'description' => 'برتقال معصور طازج عند الطلب.',
                                'price' => 11.00,
                                'image' => 'demo/dishes/orange-juice.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'علبة ثومية المعلم الخاصة (كبيرة)',
                                'description' => 'صلصة ثوم ناصعة البياض بقوام كريمي ونكهة مضبوطة.',
                                'price' => 4.00,
                                'image' => 'demo/dishes/garlic-sauce.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'علبة صوص حار مدخن',
                                'description' => 'صوص فلفل أحمر شطة مع نكهة تدخين خفيفة.',
                                'price' => 3.00,
                                'image' => 'demo/dishes/spicy-sauce.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'مشروب غازي كولا بارد',
                                'description' => 'علبة كولا مثلجة ٣٣٠ مل.',
                                'price' => 4.00,
                                'image' => 'demo/dishes/cola.jpg',
                                'addons' => [],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'user' => [
                    'name' => 'حاشي باشا المذاق',
                    'email' => 'hashi-basha@sufra.test',
                ],
                'restaurant' => [
                    'name' => 'حاشي باشا المذاق',
                    'slug' => 'hashi-basha',
                    'description' => 'مختصون في لحم الحاشي الصغير الطازج: مضغوط حاشي، كبسة برية، ومقلقل.',
                    'cuisine' => 'لحوم حاشي وشعبي',
                    'district' => 'الملز',
                    'logo' => 'demo/restaurants/hashi-logo.jpg',
                    'cover' => 'demo/restaurants/hashi-cover.jpg',
                    'min_order' => 40.00,
                    'delivery_fee' => 12.00,
                    'prep_time' => 30,
                    'is_open' => true,
                    'is_active' => true,
                ],
                'categories' => [
                    [
                        'name' => 'أطباق لحم الحاشي',
                        'sort_order' => 1,
                        'items' => [
                            [
                                'name' => 'مضغوط حاشي بلدي (نفر)',
                                'description' => 'لحم قعود صغير مطهو بقدر الضغط مع الأرز المسبك بالطماطم والفلفل الحار.',
                                'price' => 54.00,
                                'image' => 'demo/dishes/madghoot-hashi.jpg',
                                'addons' => [
                                    ['name' => 'شطة حمراء حارة', 'extra_price' => 2.50],
                                    ['name' => 'رز مضغوط زيادة', 'extra_price' => 7.00],
                                ],
                            ],
                            [
                                'name' => 'كبسة حاشي برية بالأرز البني',
                                'description' => 'كبسة البر التقليدية بالبصل المكرمل وبهارات الكبسة الخشنة.',
                                'price' => 52.00,
                                'image' => 'demo/dishes/kabsa-hashi-wild.jpg',
                                'addons' => [
                                    ['name' => 'مكسرات محمصة وكشمش', 'extra_price' => 5.00],
                                ],
                            ],
                            [
                                'name' => 'حاشي حنيذ بالقصدير',
                                'description' => 'لحم حاشي متبل مكمور ومحمر مع الأرز الأبيض.',
                                'price' => 58.00,
                                'image' => 'demo/dishes/hashi-haneeth.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'مضغوط حاشي لشخصين (صحن مزدوج)',
                                'description' => 'وجبة مشبعة تكفي شخصين مع قطع لحم وفيرة وسلطات.',
                                'price' => 102.00,
                                'image' => 'demo/dishes/madghoot-hashi-double.jpg',
                                'addons' => [
                                    ['name' => 'لبن رائب عائلي (لتر)', 'extra_price' => 8.00],
                                ],
                            ],
                        ],
                    ],
                    [
                        'name' => 'المقلقل والكباري',
                        'sort_order' => 2,
                        'items' => [
                            [
                                'name' => 'مقلقل لحم حاشي بالصاج',
                                'description' => 'قطع لحم حاشي صغيرة هبر مع بصل وفلفل أسود وكمون.',
                                'price' => 38.00,
                                'image' => 'demo/dishes/mqalqal-hashi-plate.jpg',
                                'addons' => [
                                    ['name' => 'خبز تميس بر حار (حبتين)', 'extra_price' => 3.00],
                                ],
                            ],
                            [
                                'name' => 'كبدة حاشي طازجة على الصاج',
                                'description' => 'كبدة حاشي طازجة مذبوحة يومياً سريعة الحمس بالبصل.',
                                'price' => 34.00,
                                'image' => 'demo/dishes/liver-hashi.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'شوربة مرقة حاشي غنية',
                                'description' => 'شوربة دسمة من مرق عظام ولحم الحاشي بنكهة الهيل.',
                                'price' => 8.00,
                                'image' => 'demo/dishes/soup-hashi.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'سلطة خضراء بلدية',
                                'description' => 'خس، خيار، طماطم، ونعناع بلدي بزيت الزيتون.',
                                'price' => 6.00,
                                'image' => 'demo/dishes/salad-green.jpg',
                                'addons' => [],
                            ],
                        ],
                    ],
                    [
                        'name' => 'المشروبات والتحلية',
                        'sort_order' => 3,
                        'items' => [
                            [
                                'name' => 'لبن ناقة مبستر طازج',
                                'description' => 'حليب إبل مبستر ومبرد ومغذي.',
                                'price' => 9.00,
                                'image' => 'demo/dishes/camel-milk.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'كنافة نابلسية بالجبن العكاوي',
                                'description' => 'كنافة خشنة دافئة بجبنة عكاوية تمط وتذوب بالفم.',
                                'price' => 20.00,
                                'image' => 'demo/dishes/kunafa-cheese.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'شاي حطب مدخن',
                                'description' => 'شاي مخدر على حطب السمر الطبيعي بنكهة مدخنة أصيلة.',
                                'price' => 5.00,
                                'image' => 'demo/dishes/tea-wood.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'ماء نقي معبأ',
                                'description' => 'مياه شرب نقية مبردة.',
                                'price' => 1.50,
                                'image' => 'demo/dishes/water.jpg',
                                'addons' => [],
                            ],
                        ],
                    ],
                ],
            ],
            [
                'user' => [
                    'name' => 'حلويات رشة هيل',
                    'email' => 'rashet-hail@sufra.test',
                ],
                'restaurant' => [
                    'name' => 'حلويات رشة هيل',
                    'slug' => 'rashet-hail',
                    'description' => 'أصناف الحلا السعودي والمبتكر: حنيني، بسبوسة محشية، كيكة زعفران، وتشيز كيك تمر.',
                    'cuisine' => 'حلويات ومخبوزات',
                    'district' => 'الروضة',
                    'logo' => 'demo/restaurants/hail-logo.jpg',
                    'cover' => 'demo/restaurants/hail-cover.jpg',
                    'min_order' => 30.00,
                    'delivery_fee' => 11.00,
                    'prep_time' => 20,
                    'is_open' => true,
                    'is_active' => true,
                ],
                'categories' => [
                    [
                        'name' => 'الحلويات الشرقية والشعبية',
                        'sort_order' => 1,
                        'items' => [
                            [
                                'name' => 'بسبوسة محشية قشطة طازجة',
                                'description' => 'بسبوسة ذهبية هشة محشوة بطبقة قشطة سميكة ومسقية بشيرة ماء الورد.',
                                'price' => 24.00,
                                'image' => 'demo/dishes/basbousa-cream.jpg',
                                'addons' => [
                                    ['name' => 'فستق حلبي ناعم إضافي', 'extra_price' => 4.00],
                                    ['name' => 'شيرة زعفران جانبية', 'extra_price' => 2.50],
                                ],
                            ],
                            [
                                'name' => 'صحن حنيني رشة هيل الفاخر',
                                'description' => 'حنيني ناعم بالزبدة والتمر الخلاص مع رشة هيل وزنجبيل وقشطة.',
                                'price' => 28.00,
                                'image' => 'demo/dishes/haneeni-luxury.jpg',
                                'addons' => [
                                    ['name' => 'مكسرات مشكلة محمصة', 'extra_price' => 5.00],
                                ],
                            ],
                            [
                                'name' => 'أم علي بالقشطة والمكسرات الذهبية',
                                'description' => 'حلا أم علي دافئ خارج من الفرن مع لوز وفستق وزبيب.',
                                'price' => 22.00,
                                'image' => 'demo/dishes/om-ali-pot.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'عيش السرايا بالقشطة والفستق',
                                'description' => 'طبقات توست مكرمل ومحلى بالقشطة البلدية وماء الزهر.',
                                'price' => 20.00,
                                'image' => 'demo/dishes/aish-saraya.jpg',
                                'addons' => [],
                            ],
                        ],
                    ],
                    [
                        'name' => 'الكيك والتشيز كيك المعاصر',
                        'sort_order' => 2,
                        'items' => [
                            [
                                'name' => 'كيكة الحليب بالزعفران الملكية',
                                'description' => 'كيكة إسفنجية خفيفة مسقية بحليب الزعفران الغني ومغطاة بكريمة خفيفة.',
                                'price' => 26.00,
                                'image' => 'demo/dishes/saffron-milk-cake.jpg',
                                'addons' => [
                                    ['name' => 'صوص حليب زعفران إضافي', 'extra_price' => 4.00],
                                ],
                            ],
                            [
                                'name' => 'تشيز كيك التمر والهيل',
                                'description' => 'تشيز كيك كريمي بارد على قاعدة بسكويت بصلصة التمر والكراميل.',
                                'price' => 27.00,
                                'image' => 'demo/dishes/dates-cheesecake.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'كيكة كوكيز بالشوكولاتة البلجيكية',
                                'description' => 'قطعة كوكيز طازجة وطرية محشوة بحبيبات شوكولاتة بلجيكية فاخرة.',
                                'price' => 18.00,
                                'image' => 'demo/dishes/cookie-cake.jpg',
                                'addons' => [
                                    ['name' => 'كرة آيس كريم فانيليا', 'extra_price' => 5.00],
                                ],
                            ],
                            [
                                'name' => 'تارت البيكان والكراميل المملح',
                                'description' => 'تارت مقرمش محشو بجوز البيكان الأمريكي وصوص الكراميل المملح.',
                                'price' => 24.00,
                                'image' => 'demo/dishes/pecan-tart.jpg',
                                'addons' => [],
                            ],
                        ],
                    ],
                    [
                        'name' => 'المشروبات والقهوة المختصة',
                        'sort_order' => 3,
                        'items' => [
                            [
                                'name' => 'دلة قهوة سعودية ملكية بالهيل والزعفران',
                                'description' => 'دلة قهوة نجدية فاخرة تقدم مع سكر نبات وتمر خلاص مجاناً.',
                                'price' => 28.00,
                                'image' => 'demo/dishes/saudi-coffee-set.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'سبانش لاتيه بارد',
                                'description' => 'إسبريسو مع حليب مكثف ومحلى بارد مع الثلج.',
                                'price' => 19.00,
                                'image' => 'demo/dishes/spanish-latte.jpg',
                                'addons' => [
                                    ['name' => 'شوت إسبريسو إضافي', 'extra_price' => 4.00],
                                ],
                            ],
                            [
                                'name' => 'قهوة مقطرة V60 إثيوبية',
                                'description' => 'قهوة مختصة مقطرة يدوياً بنوتات زهرية وفاكهية واضحة.',
                                'price' => 18.00,
                                'image' => 'demo/dishes/v60-coffee.jpg',
                                'addons' => [],
                            ],
                            [
                                'name' => 'شاي خادر بالنعناع المديني',
                                'description' => 'شاي أسود منقى مع ورقات نعناع مديني عطرية.',
                                'price' => 7.00,
                                'image' => 'demo/dishes/mint-tea.jpg',
                                'addons' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        foreach ($restaurantsData as $data) {
            // ١. إنشاء أو تحديث مستخدم المطعم (UserRole::Restaurant)
            $user = User::firstOrNew(['email' => $data['user']['email']]);
            $user->forceFill([
                'name' => $data['user']['name'],
                'password' => Hash::make('password'),
                'role' => UserRole::Restaurant,
                'email_verified_at' => now(),
            ])->save();

            // ٢. إنشاء أو تحديث بيانات المطعم
            $restaurantData = $data['restaurant'];
            $restaurantData['user_id'] = $user->id;

            $restaurant = Restaurant::updateOrCreate(
                ['slug' => $restaurantData['slug']],
                $restaurantData
            );

            // ٣. إنشاء التصنيفات والأصناف والإضافات التابعة للمطعم
            foreach ($data['categories'] as $catData) {
                $category = Category::updateOrCreate(
                    [
                        'restaurant_id' => $restaurant->id,
                        'name' => $catData['name'],
                    ],
                    [
                        'sort_order' => $catData['sort_order'],
                    ]
                );

                foreach ($catData['items'] as $itemData) {
                    $addons = $itemData['addons'] ?? [];
                    unset($itemData['addons']);

                    $itemData['category_id'] = $category->id;

                    $menuItem = MenuItem::updateOrCreate(
                        [
                            'category_id' => $category->id,
                            'name' => $itemData['name'],
                        ],
                        $itemData
                    );

                    // إضافة الإضافات التابعة للصنف إن وُجدت
                    foreach ($addons as $addonData) {
                        Addon::updateOrCreate(
                            [
                                'menu_item_id' => $menuItem->id,
                                'name' => $addonData['name'],
                            ],
                            [
                                'extra_price' => $addonData['extra_price'],
                            ]
                        );
                    }
                }
            }
        }
    }
}

