<?php

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

require __DIR__ . '/../../vendor/autoload.php';

$app = require __DIR__ . '/../../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$now = now();
$columns = collect(Schema::getColumnListing('products'))->flip();
$categoryIds = DB::table('categories')->orderBy('id')->pluck('id')->values();

if ($categoryIds->isEmpty()) {
    throw new RuntimeException('No category found. Add at least one category before adding products.');
}

$owners = [
    'vendor' => DB::table('users')->where('email', 'hanifrohan7@gmail.com')->first(),
    'reseller' => DB::table('users')->where('email', 'hanifsangkate7@gmail.com')->first(),
];

foreach ($owners as $type => $owner) {
    if (! $owner) {
        throw new RuntimeException("Owner user not found for {$type}.");
    }
}

$products = [
    'vendor' => [
        ['Tangail Handloom Saree', 'Premium Tangail handloom saree from Bangladesh', 1450, 1750, '1 piece', 'Fashion'],
        ['Jamdani Saree', 'Traditional jamdani saree with fine woven motifs', 4200, 5200, '1 piece', 'Fashion'],
        ['Nakshi Kantha Quilt', 'Hand stitched nakshi kantha quilt for home use', 1800, 2300, '1 piece', 'Home'],
        ['Cotton Lungi', 'Soft cotton lungi made for everyday comfort', 420, 550, '1 piece', 'Fashion'],
        ['Cotton Panjabi', 'Comfortable cotton panjabi for casual wear', 950, 1250, '1 piece', 'Fashion'],
        ['Leather Sandal', 'Locally crafted leather sandal for men', 850, 1150, '1 pair', 'Footwear'],
        ['Jute Shopping Bag', 'Eco friendly jute shopping bag with strong handle', 240, 350, '1 piece', 'Jute'],
        ['Clay Dinner Set', 'Handmade clay dinner set for traditional dining', 1100, 1500, '1 set', 'Home'],
        ['Bamboo Basket Set', 'Natural bamboo basket set for storage and decor', 520, 750, '1 set', 'Home'],
        ['Brass Serving Bowl', 'Polished brass bowl for serving and decoration', 780, 1050, '1 piece', 'Home'],
        ['Rickshaw Art Frame', 'Colorful rickshaw art wall frame from Dhaka', 620, 890, '1 piece', 'Decor'],
        ['Shital Pati Mat', 'Cool handwoven shital pati mat for summer', 950, 1250, '1 piece', 'Home'],
        ['Khadi Cotton Shirt', 'Breathable khadi cotton shirt made in Bangladesh', 780, 1050, '1 piece', 'Fashion'],
        ['Gamcha Towel Pack', 'Colorful cotton gamcha towel pack', 260, 380, '3 pieces', 'Home'],
        ['Wooden Spice Box', 'Wooden spice box with multiple compartments', 690, 950, '1 piece', 'Kitchen'],
        ['Clay Tea Cup Set', 'Traditional clay tea cup set for tea lovers', 340, 480, '6 pieces', 'Kitchen'],
        ['Leather Wallet', 'Genuine leather wallet made by local artisans', 520, 760, '1 piece', 'Fashion'],
        ['Jute Laptop Sleeve', 'Protective jute laptop sleeve for daily use', 460, 680, '1 piece', 'Jute'],
        ['Nakshi Cushion Cover', 'Hand embroidered nakshi cushion cover', 390, 560, '2 pieces', 'Home'],
        ['Jamdani Three Piece', 'Elegant jamdani three piece dress material', 2600, 3300, '1 set', 'Fashion'],
        ['Batik Cotton Saree', 'Colorful batik cotton saree from Bangladesh', 1250, 1650, '1 piece', 'Fashion'],
        ['Handmade Clay Vase', 'Decorative handmade clay vase for living room', 720, 980, '1 piece', 'Decor'],
        ['Cane Stool', 'Strong cane stool for home and balcony', 1150, 1500, '1 piece', 'Furniture'],
        ['Jute Table Runner', 'Natural jute table runner for dining table', 360, 520, '1 piece', 'Home'],
        ['Wooden Jewelry Box', 'Handcrafted wooden jewelry box with polish finish', 880, 1200, '1 piece', 'Decor'],
    ],
    'reseller' => [
        ['Miniket Rice 5kg', 'Quality Bangladeshi miniket rice pack', 420, 520, '5 kg', 'Grocery'],
        ['Kataribhog Rice 2kg', 'Aromatic kataribhog rice from Bangladesh', 260, 340, '2 kg', 'Grocery'],
        ['Chinigura Rice 1kg', 'Fine chinigura aromatic rice for pulao and kheer', 170, 230, '1 kg', 'Grocery'],
        ['Mustard Oil 1L', 'Pure mustard oil for traditional cooking', 260, 330, '1 liter', 'Grocery'],
        ['Black Tea 500g', 'Fresh Bangladeshi black tea from Sylhet gardens', 310, 420, '500 g', 'Grocery'],
        ['Date Molasses', 'Natural khejur gur date molasses', 280, 380, '1 kg', 'Grocery'],
        ['Patali Gur', 'Traditional patali gur from rural Bangladesh', 240, 330, '1 kg', 'Grocery'],
        ['Flattened Rice Chira', 'Clean flattened rice for breakfast and snacks', 110, 160, '1 kg', 'Grocery'],
        ['Puffed Rice Muri', 'Crispy muri for snacks and chanachur mix', 90, 140, '1 kg', 'Grocery'],
        ['Red Lentil Dal', 'Premium masoor dal for daily cooking', 145, 190, '1 kg', 'Grocery'],
        ['Mung Dal', 'Clean mung dal for healthy meals', 165, 220, '1 kg', 'Grocery'],
        ['Turmeric Powder', 'Ground turmeric powder for Bangladeshi recipes', 90, 130, '250 g', 'Grocery'],
        ['Chili Powder', 'Red chili powder with strong flavor', 95, 140, '250 g', 'Grocery'],
        ['Coriander Powder', 'Fresh coriander powder for cooking', 85, 125, '250 g', 'Grocery'],
        ['Mixed Pickle Jar', 'Spicy mixed pickle jar for rice and snacks', 180, 250, '500 g', 'Grocery'],
        ['Chanachur Pack', 'Crunchy chanachur snack pack', 120, 170, '500 g', 'Snacks'],
        ['Dry Fish Shutki', 'Selected dry fish shutki for traditional dishes', 360, 480, '500 g', 'Grocery'],
        ['Puffed Rice Moa', 'Sweet moa made with puffed rice and gur', 150, 220, '12 pieces', 'Snacks'],
        ['Coconut Naru', 'Traditional coconut naru sweet pack', 190, 260, '12 pieces', 'Snacks'],
        ['Handmade Semai', 'Fine handmade semai for dessert', 115, 170, '500 g', 'Grocery'],
        ['Soybean Oil 2L', 'Cooking soybean oil family pack', 380, 470, '2 liter', 'Grocery'],
        ['Local Honey 500g', 'Natural local honey collected in Bangladesh', 420, 550, '500 g', 'Grocery'],
        ['Achar Masala Mix', 'Ready achar masala mix for homemade pickle', 75, 115, '200 g', 'Grocery'],
        ['Biscuit Family Pack', 'Tea time biscuit family pack', 160, 230, '1 pack', 'Snacks'],
        ['Handmade Papad', 'Crispy handmade papad for frying', 130, 190, '500 g', 'Grocery'],
    ],
];

function pickCategoryId($categoryIds, int $index): int
{
    return (int) $categoryIds[$index % $categoryIds->count()];
}

function productImage(string $name, string $group, int $index): string
{
    $file = 'bd-product-' . Str::slug($group . '-' . $name) . '.png';
    $paths = [
        storage_path('app/public/products/' . $file),
        public_path('storage/products/' . $file),
    ];

    foreach ($paths as $path) {
        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0775, true);
        }
    }

    $width = 900;
    $height = 900;
    $image = imagecreatetruecolor($width, $height);
    $palettes = [
        [[245, 247, 250], [36, 94, 79], [230, 180, 72]],
        [[250, 247, 239], [120, 53, 45], [42, 116, 91]],
        [[244, 248, 246], [43, 78, 132], [218, 92, 64]],
        [[249, 249, 242], [95, 73, 51], [36, 128, 144]],
    ];
    [$bgRgb, $primaryRgb, $accentRgb] = $palettes[$index % count($palettes)];
    $bg = imagecolorallocate($image, ...$bgRgb);
    $primary = imagecolorallocate($image, ...$primaryRgb);
    $accent = imagecolorallocate($image, ...$accentRgb);
    $white = imagecolorallocate($image, 255, 255, 255);
    $dark = imagecolorallocate($image, 31, 41, 55);
    $muted = imagecolorallocate($image, 107, 114, 128);

    imagefilledrectangle($image, 0, 0, $width, $height, $bg);
    imagefilledellipse($image, 450, 360, 420, 300, $primary);
    imagefilledellipse($image, 450, 360, 300, 210, $accent);
    imagefilledrectangle($image, 285, 330, 615, 585, $white);
    imagerectangle($image, 285, 330, 615, 585, $primary);
    imagefilledrectangle($image, 330, 245, 570, 330, $accent);
    imagerectangle($image, 330, 245, 570, 330, $primary);

    imagestring($image, 5, 365, 385, strtoupper($group), $primary);
    $lines = explode("\n", wordwrap($name, 18, "\n", true));
    $y = 435;
    foreach (array_slice($lines, 0, 3) as $line) {
        imagestring($image, 5, (int) max(30, 450 - strlen($line) * 5), $y, $line, $dark);
        $y += 35;
    }
    imagestring($image, 3, 340, 650, 'Made for Bangladesh market', $muted);

    foreach ($paths as $path) {
        imagepng($image, $path, 6);
    }
    imagedestroy($image);

    return 'products/' . $file;
}

$inserted = ['vendor' => 0, 'reseller' => 0];
$skipped = ['vendor' => 0, 'reseller' => 0];

DB::transaction(function () use ($products, $owners, $categoryIds, $columns, $now, &$inserted, &$skipped) {
    foreach ($products as $type => $items) {
        foreach ($items as $index => [$name, $title, $buyingPrice, $price, $unit, $badge]) {
            $owner = $owners[$type];

            $exists = DB::table('products')
                ->where('user_id', $owner->id)
                ->where('title', $title)
                ->exists();

            if ($exists) {
                $skipped[$type]++;
                continue;
            }

            $thumbnail = productImage($name, $type, $index);
            $slug = Str::slug($title . '-' . $owner->id);
            $data = [
                'name' => $name,
                'title' => $title,
                'slug' => $slug,
                'description' => $title . '. This Bangladeshi product is listed with local delivery options and reliable quality.',
                'price' => $price,
                'discount' => 0,
                'buying_price' => (string) $buyingPrice,
                'thumbnail' => $thumbnail,
                'video' => null,
                'unit' => $unit,
                'offer_type' => 0,
                'category_id' => pickCategoryId($categoryIds, $index),
                'user_id' => $owner->id,
                'belongs_to_type' => $type,
                'status' => 'Active',
                'display_at_home' => 1,
                'country' => $owner->country ?? 'Bangladesh',
                'state' => $owner->state ?? 'Dhaka',
                'meta_title' => $title,
                'meta_description' => $title . ' available in Bangladesh.',
                'keyword' => Str::slug($name, ','),
                'meta_tags' => json_encode([$badge, 'Bangladesh', $type]),
                'meta_thumbnail' => $thumbnail,
                'vc' => '0',
                'brand' => 'Local Bangladesh',
                'cod' => 1,
                'courier' => 1,
                'hand' => 1,
                'shipping_in_dhaka' => '80',
                'shipping_out_dhaka' => '120',
                'shipping_note' => 'Delivery available inside and outside Dhaka.',
                'badge' => json_encode([$badge, 'New']),
                'tags' => json_encode([$badge, 'Bangladeshi product', $type]),
                'accept_cuppon' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $data = array_filter(
                $data,
                fn($value, $key) => $columns->has($key),
                ARRAY_FILTER_USE_BOTH
            );

            $productId = DB::table('products')->insertGetId($data);

            if (Schema::hasTable('product_has_attributes')) {
                DB::table('product_has_attributes')->insert([
                    'product_id' => (string) $productId,
                    'name' => 'Unit',
                    'value' => $unit,
                    'stock' => '100',
                    'price' => (string) $price,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            if (Schema::hasTable('product_has_images')) {
                DB::table('product_has_images')->insert([
                    'product_id' => (string) $productId,
                    'image' => $thumbnail,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $inserted[$type]++;
        }
    }
});

echo json_encode([
    'inserted' => $inserted,
    'skipped_existing' => $skipped,
    'total_products' => DB::table('products')->count(),
], JSON_PRETTY_PRINT) . PHP_EOL;
