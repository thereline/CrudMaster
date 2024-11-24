<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\Year;

/**
 * @template TModel of \Workbench\App\Models\Year
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class YearFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = Year::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['2022-2023', '2024-2025']),
        ];
    }
}
