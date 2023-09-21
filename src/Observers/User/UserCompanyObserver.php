<?php

namespace Bildvitta\IssSupernova\Observers\User;

use Illuminate\Support\Facades\Config;

class UserCompanyObserver
{
    public function created($userCompany)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $userCompany->loadMissing(
            'user',
        );

        if ($userCompany->user) {
            event('eloquent.updated: App\Models\User', $userCompany->user);
        }
    }

    public function updated($userCompany)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $userCompany->loadMissing(
            'user',
        );

        if ($userCompany->user) {
            event('eloquent.updated: App\Models\User', $userCompany->user);
        }
    }

    public function deleted($user)
    {
        $this->updated($user);
    }
}
