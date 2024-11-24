<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\School;
use Workbench\App\Models\SchoolSession;
use Workbench\App\Models\Semester;
use Workbench\App\Models\Year;

/**
 * @template TModel of \Workbench\App\Models\SchoolSession
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class SchoolSessionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = SchoolSession::class;

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
            'semester_id' => function () {
                if (Semester::count()) {
                    return $this->faker->randomElement(Semester::query()->pluck('id')->toArray());
                } else {
                    return Semester::factory()->create()->id;
                }
            },
            'year_id' => function () {
                if (Year::count()) {
                    return $this->faker->randomElement(Year::query()->pluck('id')->toArray());
                } else {
                    return Year::factory()->create()->id;
                }
            },
        ];
    }
}
