<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\Semester;

/**
 * @template TModel of \Workbench\App\Models\Semester
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class SemesterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = Semester::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['First', 'Second']),
        ];
    }
}
