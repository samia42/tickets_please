<?php

namespace App\Providers;

use App\Models\Ticket;
use App\Policies\V1\TicketPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register() : void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot() : void
    {

    }
}
