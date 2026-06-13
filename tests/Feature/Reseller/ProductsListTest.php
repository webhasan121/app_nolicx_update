<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;

it('shows reseller products that were created before today', function () {
    $user = User::factory()->create();

    Permission::findOrCreate('access_reseller_dashboard', 'web');
    Permission::findOrCreate('product_view', 'web');
    $user->givePermissionTo(['access_reseller_dashboard', 'product_view']);

    $this->actingAs($user);

    $category = Category::create([
        'user_id' => $user->id,
        'name' => 'Test Category',
        'slug' => 'test-category',
    ]);

    $product = Product::create([
        'name' => 'Older reseller product',
        'title' => 'Older reseller product',
        'category_id' => $category->id,
        'belongs_to_type' => 'reseller',
        'price' => 1200,
        'unit' => 3,
    ]);

    $product->forceFill([
        'created_at' => now()->subMonth(),
        'updated_at' => now()->subMonth(),
    ])->save();

    $this->get(route('reseller.products.list'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Reseller/Products/Index')
            ->has('products.data', 1)
            ->where('products.data.0.id', $product->id)
            ->where('products.total', 1)
        );
});
