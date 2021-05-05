<?php

namespace App\Repositories;

use App\Exceptions\StudentNotFoundException;
use App\Exceptions\UserNotFoundException;
use App\Models\Student;
use App\Models\User;
use App\Validators\StudentValidator;
use Database\Factories\UserFactory;
use DB;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Log;
use Throwable;

class StudentRepository
{
    /**
     * @var StudentValidator
     */
    private StudentValidator $studentValidator;

    /**
     * @var UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(
        StudentValidator $studentValidator,
        UserRepository $userRepository
    ) {
        $this->studentValidator = $studentValidator;
        $this->userRepository = $userRepository;
    }

    /**
     * @param int $id
     * @return Student
     * @throws StudentNotFoundException
     */
    public function getById(int $id): Student
    {
        try {
            $student = Student::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new StudentNotFoundException($e->getMessage());
        }

        return $student;
    }

    /**
     * @param string $email
     * @return Student
     * @throws StudentNotFoundException
     */
    public function get(string $email): Student
    {
        try {
            $student = $this->userRepository->get($email)->student;
        } catch (ModelNotFoundException | UserNotFoundException $e) {
            Log::error("Studente con email $email non trovato");
            throw new StudentNotFoundException("Studente con email $email non trovato");
        }

        return $student;
    }

    /**
     * @return Student[]|Collection
     */
    public function all()
    {
        return Student::all();
    }

    /**
     * @param Student $student
     * @param string $email
     * @param string $password
     * @return Student
     * @throws Throwable
     * @throws ValidationException
     */
    public function saveOrCreate(Student $student, string $email, string $password): Student
    {
        try {
            DB::beginTransaction();
            /** @var User $user */
            $user = UserFactory::new()->make([
                'name' => $student->first_name . ' ' . $student->last_name,
                'email' => $email,
                'password' => $password,
                'id' => $student->user ? $student->user_id : null
            ]);
            $user = $this->userRepository->saveOrCreate($user);
            $student->user_id = $user->id;
            $newStudent = $this->assignAndSave(
                $student->id ? $this->getById($student->id) : Student::factory()->newModel(),
                $student
            );
            DB::commit();
            return $newStudent;
        } catch (ValidationException $validationException) {
            DB::rollBack();
            Log::error("Student saveOrCreate Validation:\n" . $validationException->getMessage());
            Log::error(print_r($validationException->errors(), true));
            throw $validationException;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error("Student saveOrCreate:\n" . $exception->getMessage());
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param Student $student
     * @param string $email
     * @param ?string $password
     * @return Student
     * @throws Throwable
     * @throws ValidationException
     */
    public function save(Student $student, string $email, ?string $password = null): Student
    {
        try {
            DB::beginTransaction();
            $oldStudent = $this->getById($student->id);
            /** @var User $user */
            $user = UserFactory::new()->make([
                'name' => $student->first_name . ' ' . $student->last_name,
                'email' => $email,
                'password' => $password,
                'id' => $oldStudent->user->id
            ]);
            $this->userRepository->save($user);
            $newStudent = $this->assignAndSave($oldStudent, $student);
            DB::commit();
            return $newStudent;
        } catch (ValidationException $validationException) {
            DB::rollBack();
            Log::error("Student save Validation:\n" . $validationException->getMessage());
            Log::error(print_r($validationException->errors(), true));
            throw $validationException;
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error("Student save:\n" . $exception->getMessage());
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param Student $student
     * @throws Throwable
     */
    public function delete(Student $student): void
    {
        try {
            DB::beginTransaction();
            $student->delete();
            $student->user->delete();
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            Log::error("Student delete:\n" . $exception->getMessage());
            throw new Exception($exception->getMessage());
        }
    }

    /**
     * @param Student $newStudent
     * @param Student $student
     * @return Student
     * @throws StudentNotFoundException
     * @throws ValidationException
     */
    private function assignAndSave(Student $newStudent, Student $student): Student
    {
        $newStudent->first_name = $student->first_name;
        $newStudent->last_name = $student->last_name;
        $newStudent->age = $student->age;
        $newStudent->user_id = $student->user_id;

        $this->studentValidator->validateData($newStudent->toArray(), ['student' => $newStudent]);
        $newStudent->save();
        return $this->get($newStudent->email);
    }
}
