<?php

namespace Bildvitta\IssSupernova\Observers\Customer;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class FgtsAccountObserver
{
    public function created($fgtsAccount)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $fgtsAccount->loadMissing(
            'customer',
        );
        if ($fgtsAccount->customer) {
            $fgtsAccount->customer->loadMissing(
                'related_customer'
            );
        }
        $data = $fgtsAccount->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerFgtsAccounts()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($fgtsAccount)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $fgtsAccount->loadMissing(
            'customer',
        );
        if ($fgtsAccount->customer) {
            $fgtsAccount->customer->loadMissing(
                'related_customer'
            );
        }
        $data = $fgtsAccount->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerFgtsAccounts()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($fgtsAccount)
    {
        $this->updated($fgtsAccount);
    }
}
