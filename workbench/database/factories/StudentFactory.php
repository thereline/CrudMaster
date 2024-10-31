<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\School;
use Workbench\App\Models\Student;

/**
 * @template TModel of \Workbench\App\Models\Student
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class StudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $school_id = function () {
            if (School::count()) {
                return $this->faker->randomElement(School::pluck('id')->toArray());
            } else {
                return School::factory()->create()->id;
            }
        }; // or null,

        return [
            'school_id'=>$school_id,
            'first_name'=>$this->faker->firstName,
            'last_name'=>$this->faker->lastName,
            'active'=>$this->faker->boolean(100),
        ];
    }
}
