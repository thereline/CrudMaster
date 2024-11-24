<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\School;
use Workbench\App\Models\Teacher;

/**
 * @template TModel of \Workbench\App\Models\Teacher
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class TeacherFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = Teacher::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'school_id' => function () {
                if (School::count()) {
                    return $this->faker->randomElement(School::query()->pluck('id')->toArray());
                } else {
                    return School::factory()->create()->id;
                }
            },
            'name' => $this->faker->firstName,

        ];
    }
}
