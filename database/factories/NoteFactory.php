<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Note;
use Faker\Generator as Faker;

$factory->define(Note::class, function (Faker $faker) {
    $uids = \App\User::all()->pluck('id')->toArray();
    return [
        'title' => $faker->text(20),
        'content' => $faker->text(),
        'user_id' => rand(1,10)
    ];
});
