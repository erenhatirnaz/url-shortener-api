<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Shortcut;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShortcutFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Shortcut::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'shortcut' => Str::random(5),
            'url' => $this->faker->url,
        ];
    }
}
