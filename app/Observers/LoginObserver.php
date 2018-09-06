<?php

namespace App\Observers;

use App\Login;
use Illuminate\Support\Facades\Auth;

class LoginObserver
{
    /**
     * Handle the login "creating" event.
     *
     * @param  \App\Login  $login
     * @return void
     */
    public function creating(Login $login)
    {
        $login->created_by = Auth::guard('api')->user() ? Auth::guard('api')->user()->id : 1;
    }

    /**
     * Handle the login "updating" event.
     *
     * @param  \App\Login  $login
     * @return void
     */
    public function updating(Login $login)
    {
        $login->updated_by = Auth::guard('api')->user() ? Auth::guard('api')->user()->id : 1;

    }
}
