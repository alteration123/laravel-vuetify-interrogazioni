<?php

namespace App\Validators;

use App\Models\Student;
use Illuminate\Validation\Rule;

/*
 * EXAMPLE::PROGETTO validation studente
 */
class StudentValidator extends EntityValidator
{
    protected function getMessages($extendParameters)
    {
        return [
            'residence_region.required' => 'Campo Obbligatorio'
        ];
    }

    /**
     * @param $extendParameters
     * @return array
     */
    protected function getRules($extendParameters)
    {
        /** @var Student $administrator */
        $student = array_key_exists('student', $extendParameters) ?
            $extendParameters['student'] : Student::factory()->newModel();
        return [
            'first_name' => ['required', 'max:20'],
            'last_name' => ['required', 'max:20'],
            'email' => ['required',  Rule::unique('students', 'email')->ignore($student),
                'email', 'max:50'],
            'age' => ['required', 'min:10', 'max:18'],
            'user_id' => ['required', Rule::unique('students', 'user_id')->ignore($student),
                Rule::exists('students', 'id'), 'user_not_assigned:student'],
        ];
    }
}
