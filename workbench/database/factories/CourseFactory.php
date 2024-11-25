<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\Course;
use Workbench\App\Models\Level;
use Workbench\App\Models\SchoolSession;
use Workbench\App\Models\Subject;
use Workbench\App\Models\Teacher;

/**
 * @template TModel of \Workbench\App\Models\Course
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'session_id' => function () {
                if (SchoolSession::count()) {
                    return $this->faker->randomElement(SchoolSession::query()->pluck('id')->toArray());
                } else {
                    return SchoolSession::factory()->create()->id;
                }
            },
            'teacher_id' => function () {
                if (Teacher::count()) {
                    return $this->faker->randomElement(Teacher::query()->pluck('id')->toArray());
                } else {
                    return Teacher::factory()->create()->id;
                }
            },
            'level_id' => function () {
                if (Level::count()) {
                    return $this->faker->randomElement(Level::query()->pluck('id')->toArray());
                } else {
                    return Level::factory()->create()->id;
                }
            },
            'subject_id' => function () {
                if (Subject::count()) {
                    return $this->faker->randomElement(Subject::query()->pluck('id')->toArray());
                } else {
                    return Subject::factory()->create()->id;
                }
            },
            'name' => $this->faker->word,
            'coefficient' => $this->faker->numberBetween(1, 5),
            'code' => $this->faker->word,
        ];
    }
}
