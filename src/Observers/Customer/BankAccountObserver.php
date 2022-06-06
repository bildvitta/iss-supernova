<?php

namespace Bildvitta\IssSupernova\Observers\Customer;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class BankAccountObserver
{
    public function created($bankAccount)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $bankAccount->loadMissing(
            'customer',
            'bank',
        );
        $data = $bankAccount->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerBankAccounts()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($bankAccount)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $bankAccount->loadMissing(
            'customer',
            'bank',
        );
        $data = $bankAccount->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerBankAccounts()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($bankAccount)
    {
        $this->updated($bankAccount);
    }
}
