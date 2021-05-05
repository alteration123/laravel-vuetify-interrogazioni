<?php

namespace Database\Seeders;

use App\Repositories\StudentRepository;
use Database\Factories\StudentFactory;
use Exception;
use Faker\Generator;
use Faker\Provider\it_IT\Person;
use Illuminate\Validation\ValidationException;
use Log;
use Throwable;

/*
 * EXAMPLE::PROGETTO STUDENT_SEEDER
 */
class StudentSeeder
{
    /**
     * @var StudentRepository
     */
    protected $studentRepository;
    /**
     * @var Person
     */
    protected $fakerPerson;
    /**
     * @var Generator
     */
    protected $faker;

    /**
     * StudentSeeder constructor.
     * @param StudentRepository $studentRepository
     * @param Person $fakerPerson
     * @param Generator $faker
     */
    public function __construct(
        StudentRepository $studentRepository,
        Person $fakerPerson,
        Generator $faker
    ) {
        $this->studentRepository = $studentRepository;
        $this->fakerPerson = $fakerPerson;
        $this->faker = $faker;
    }

    /**
     * @param int $numberOfStudents
     * @return int
     * @throws Throwable
     */
    public function execute(int $numberOfStudents): int
    {
        $success = 0;

        for ($i = 0; $i < $numberOfStudents; $i++) {
            $firstName = $this->fakerPerson->firstName(
                random_int(0, 1) === 0 ? Person::GENDER_MALE : Person::GENDER_FEMALE
            );
            $lastName = $this->fakerPerson->lastName();
            $email = str_replace(["'", " "],'', strtolower(
                "$firstName.$lastName."
                . $this->faker->unique()->word
                . "@"
                . $this->faker->domainName
            ));

            $toGenerateStudent = StudentFactory::new()->make(
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'age' => random_int(10, 18)
                ]
            );

            try {
                $this->studentRepository->saveOrCreate(
                    $toGenerateStudent, $email, 'student'
                );

                $success++;
            } catch (ValidationException $exception) {
                Log::error('Impossibile creare il seguente studente: ' . $exception->getMessage());
                Log::error(print_r(array_merge($toGenerateStudent->toArray(), ['email' => $email]), true));
                Log::error(print_r(array_merge($exception->errorBag), true));
            } catch (Exception $exception) {
                Log::error('Impossibile creare il seguente studente: ' . $exception->getMessage());
                Log::error(print_r(array_merge($toGenerateStudent->toArray(), ['email' => $email]), true));
            }
        }

        return $success;
    }
}
