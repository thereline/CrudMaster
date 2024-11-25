<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\Course;
use Workbench\App\Models\Score;
use Workbench\App\Models\Student;

/**
 * @template TModel of \Workbench\App\Models\Score
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class ScoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = Score::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_id' => function () {
                if (Course::count()) {
                    return $this->faker->randomElement(Course::query()->pluck('id')->toArray());
                } else {
                    return Course::factory()->create()->id;
                }
            },
            'student_id' => function () {
                if (Student::count()) {
                    return $this->faker->randomElement(Student::query()->pluck('id')->toArray());
                } else {
                    return Student::factory()->create()->id;
                }
            },
            'marks' => $this->faker->numberBetween(0, 20),
        ];
    }
}
