<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppRepositoryProvider extends ServiceProvider {

    public function boot() {}

    public function register() {
        $this->app->bind(
            \App\Repositories\Lead\LeadRepositoryContract::class,
            \App\Repositories\Lead\LeadRepository::class
        );
    }
}
