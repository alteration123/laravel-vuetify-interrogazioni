<?php

namespace App\Validators;

use Illuminate\Contracts\Validation\Factory;

abstract class EntityValidator
{
    /**
     * @param $data
     * @param array $extendParameters
     * @return mixed
     */
    public function validateData($data, $extendParameters = [])
    {
        return $this->getValidationFactory()->make(
            $data, $this->getRules($extendParameters), $this->getMessages($extendParameters)
        )->validateWithBag('validation');
    }

    abstract protected function getRules($extendParameters);

    abstract protected function getMessages($extendParameters);

    /**
     * Get a validation factory instance.
     *
     * @return Factory
     */
    protected function getValidationFactory()
    {
        return app(Factory::class);
    }
}
