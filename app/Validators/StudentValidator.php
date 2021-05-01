<?php

namespace App\Validators;

use App\Models\Student;
use Illuminate\Validation\Rule;

/*
 * EXAMPLE::PROGETTO validation studente
 */
class StudentValidator extends EntityValidator
{
    protected function getMessages($extendParameters): array
    {
        return [
        ];
    }

    /**
     * @param $extendParameters
     * @return array
     */
    protected function getRules($extendParameters): array
    {
        /** @var Student $student */
        $student = array_key_exists('student', $extendParameters) ?
            $extendParameters['student'] : Student::factory()->newModel();
        return [
            'first_name' => ['required', 'max:20'],
            'last_name' => ['required', 'max:20'],
            'age' => ['required', 'numeric', 'min:10', 'max:18'],
            'user_id' => ['required', Rule::unique('students', 'user_id')->ignore($student),
                Rule::exists('users', 'id'), 'user_not_assigned:student'],
        ];
    }
}
