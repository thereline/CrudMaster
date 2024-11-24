<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\Level;

/**
 * @template TModel of \Workbench\App\Models\Level
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class LevelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = Level::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /*// Check if any schools exist
        $level= Level::inRandomOrder()->first();
        // If no school exists, create a new one
        if (!$level) {
            $level = Level::factory()->create();
        }*/

        return [
            'name' => $this->faker->randomElement(['Form One', 'Form Two']),
        ];
    }
}
