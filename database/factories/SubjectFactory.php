<?php

namespace Database\Factories;

use App\Models\Subject;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/*
 * EXAMPLE::PROGETTO FACTORY
 */
class SubjectFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Subject::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws Exception
     */
    public function definition()
    {
        return [
            'name' => $this->faker->text(50),
            'description' => $this->faker->text(255)
        ];
    }
}
