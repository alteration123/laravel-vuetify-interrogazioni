<?php

namespace Database\Factories;

use App\Models\Student;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/*
 * EXAMPLE::PROGETTO FACTORY
 */
class StudentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Student::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws Exception
     */
    public function definition()
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'age' => random_int(10, 18),
            'user_id' => null
        ];
    }
}
