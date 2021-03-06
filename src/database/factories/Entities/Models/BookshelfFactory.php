<?php

namespace Database\Factories\Entities\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class BookshelfFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = \App\Entities\Models\Bookshelf::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name'        => $this->faker->sentence,
            'slug'        => Str::random(10),
            'description' => $this->faker->paragraph,
        ];
    }
}
