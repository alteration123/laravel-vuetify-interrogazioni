<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /*
     * EXAMPLE::PROGETTO creata custom validation
     */
    private function getEntities()
    {
        return ['student'];
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('user_not_assigned', function ($attribute, $value, $exclude) {
            /** @var User $user */
            $user = User::query()->where('id', '=', $value)->first();
            foreach ($this->getEntities() as $entity) {
                if (in_array($entity, $exclude)) {
                    continue;
                }
                if ($user->{ $entity }) {
                    return false;
                }
            }
            return true;
        });
    }
}
