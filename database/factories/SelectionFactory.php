<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Selection;
use Faker\Generator as Faker;

$factory->define(Selection::class, function (Faker $faker) {
    return [
        'match_id' => $faker->randomNumber(),
        'odds' => $faker->randomFloat(2, 1.01, 100),
    ];
});
