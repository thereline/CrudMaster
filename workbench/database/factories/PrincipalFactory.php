<?php

namespace Workbench\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Workbench\App\Models\Principal;
use Workbench\App\Models\School;

/**
 * @template TModel of \Workbench\App\Models\Principal
 *
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<TModel>
 */
class PrincipalFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<TModel>
     */
    protected $model = Principal::class;

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
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];
    }
}
