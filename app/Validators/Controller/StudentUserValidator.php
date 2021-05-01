<?php

namespace App\Validators\Controller;

use App\Models\Student;
use App\Models\User;
use App\Validators\EntityValidator;
use Illuminate\Validation\Rule;

class StudentUserValidator extends EntityValidator
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
        /** @var User $user */
        $user = array_key_exists('user', $extendParameters) ?
            $extendParameters['user'] : User::factory()->newModel();
        return [
            'first_name' => ['required', 'string', 'max:20'],
            'last_name' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string'],
            'email' => ['required', 'string',  Rule::unique('users', 'email')->ignore($user), 'email', 'max:50'],
            'age' => ['required', 'numeric', 'min:10', 'max:18'],
        ];
    }
}
