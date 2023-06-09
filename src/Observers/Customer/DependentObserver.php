<?php

namespace Bildvitta\IssSupernova\Observers\Customer;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class DependentObserver
{
    public function created($dependent)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $dependent->loadMissing(
            'customer',
            'dependent_type',
            'educational_institution',
            'educational_course',
        );
        if ($dependent->customer) {
            $dependent->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($dependent->customer->user) {
                $dependent->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $dependent->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerDependents()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($dependent)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $dependent->refresh();

        $dependent->loadMissing(
            'customer',
            'dependent_type',
            'educational_institution',
            'educational_course',
        );
        if ($dependent->customer) {
            $dependent->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($dependent->customer->user) {
                $dependent->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $dependent->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerDependents()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($dependent)
    {
        $this->updated($dependent);
    }
}
