<?php

$factory->define(\Octommerce\Octommerce\Models\Product::class, function (Faker\Generator $faker) {
    $addDiscount = $faker->boolean;

    return [
        'name' => $faker->text(25),
        'sku' => $faker->ean8,
        'type' => 'simple',
        'price' => $faker->randomDigitNotNull,
        'currency_code' => 'USD',
        'description' => $faker->paragraph(7, true),
        'discount_type' => $addDiscount ? 'percentage' : null,
        'discount_amount' => $addDiscount ? $faker->randomDigitNotNull : null,
        'tags' => factory(\Octommerce\Octommerce\Models\Tag::class, 3)->create(),
        'brand' => factory(\Octommerce\Octommerce\Models\Brand::class)->create(),
        'images' => $faker->image(storage_path() . '/temp', 400, 300, 'food', true, false),
    ];
});

$factory->define(\Octommerce\Octommerce\Models\Tag::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->word,
    ];
});

$factory->define(\Octommerce\Octommerce\Models\Brand::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->company,
    ];
});

