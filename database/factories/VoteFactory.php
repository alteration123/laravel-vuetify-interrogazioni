<?php

namespace Database\Factories;

use App\Models\Vote;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

/*
 * EXAMPLE::PROGETTO FACTORY
 */
class VoteFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Vote::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * @throws Exception
     */
    public function definition()
    {
        return [
            'score' => random_int(1, 10),
            'date' => Carbon::today(),
            'student_id' => null,
            'subject_id' => null
        ];
    }
}
