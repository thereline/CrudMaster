<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\Gender;

/**
 * @template TModel of \Workbench\App\Models\Gender
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class GenderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = Gender::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['male', 'female']),
        ];
    }
}
