<?php

namespace App\Validators;

use App\Models\User;
use Illuminate\Validation\Rule;

class UserValidator extends EntityValidator
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
        /** @var User $administrator */
        $user = array_key_exists('user', $extendParameters) ?
            $extendParameters['user'] : User::factory()->newModel();
        return [
            'name' => ['required', 'max:41'],
            'password' => ['required'],
            'email' => ['required',  Rule::unique('users', 'email')->ignore($user), 'email', 'max:50'],
        ];
    }
}
