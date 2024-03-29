<?php

namespace Bildvitta\IssSupernova\Observers\Customer;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class PersonalReferenceObserver
{
    public function created($personalReference)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $personalReference->loadMissing(
            'customer',
            'relation_type',
        );
        if ($personalReference->customer) {
            $personalReference->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($personalReference->customer->user) {
                $personalReference->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $personalReference->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerPersonalReferences()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($personalReference)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $personalReference->refresh();

        $personalReference->loadMissing(
            'customer',
            'relation_type',
        );
        if ($personalReference->customer) {
            $personalReference->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($personalReference->customer->user) {
                $personalReference->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $personalReference->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerPersonalReferences()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($personalReference)
    {
        $this->updated($personalReference);
    }
}
