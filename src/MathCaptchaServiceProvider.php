<?php

namespace Jbrathod\MathCaptcha;

use Illuminate\Support\ServiceProvider;

class MathCaptchaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('mathcaptcha', function ($app) {
            return new MathCaptcha($this->app['session']);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['validator']->extend('mathcaptcha', function ($attribute, $value, $parameters) {
            $page = $parameters[0] ?? null;

            if(!is_null($page)){
                return $this->app['mathcaptcha']->verify($value, $page);    
            }

            return $this->app['mathcaptcha']->verify($value);
        }, 'You entered wrong answer.');
    }
}
