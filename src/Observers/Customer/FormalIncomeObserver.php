<?php

namespace Bildvitta\IssSupernova\Observers\Customer;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class FormalIncomeObserver
{
    public function created($customer)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $customer->loadMissing(
            'customer',
            'occupation',
            'proof_of_income_type',
        );
        $data = $customer->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerFormalIncomes()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($customer)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $customer->loadMissing(
            'customer',
            'occupation',
            'proof_of_income_type',
        );
        $data = $customer->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerFormalIncomes()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($customer)
    {
        //
    }
}
