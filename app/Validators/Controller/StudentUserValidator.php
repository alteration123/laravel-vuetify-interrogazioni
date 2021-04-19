<?php

namespace App\Validators\Controller;

use App\Models\Student;
use App\Models\User;
use App\Validators\EntityValidator;
use Illuminate\Validation\Rule;

class StudentUserValidator extends EntityValidator
{
    protected function getMessages($extendParameters)
    {
        return [
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
        /** @var User $administrator */
        $user = array_key_exists('user', $extendParameters) ?
            $extendParameters['user'] : User::factory()->newModel();
        return [
            'first_name' => ['required', 'max:20'],
            'last_name' => ['required', 'max:20'],
            'password' => ['required'],
            'email' => ['required',  Rule::unique('users', 'email')->ignore($user), 'email', 'max:50'],
            'age' => ['required', 'numeric', 'min:10', 'max:18'],
        ];
    }
}
