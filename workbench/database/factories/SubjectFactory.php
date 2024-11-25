<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\Subject;

/**
 * @template TModel of \Workbench\App\Models\Subject
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class SubjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = Subject::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement(['Mathematics', 'English Language']),
        ];
    }
}
