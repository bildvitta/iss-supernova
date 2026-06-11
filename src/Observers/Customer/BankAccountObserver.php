<?php

namespace Bildvitta\IssSupernova\Observers\Customer;

use Bildvitta\IssSupernova\Exceptions\Customer\BankAccountException;
use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class BankAccountObserver
{
    /**
     * @throws BankAccountException
     */
    public function created($bankAccount)
    {
        if (! Config::get('iss-supernova.base_uri')) {
            return;
        }

        $bankAccount->loadMissing(
            'customer',
            'bank',
        );
        if ($bankAccount->customer) {
            $bankAccount->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($bankAccount->customer->user) {
                $bankAccount->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $bankAccount->toArray();
        $data['sync_to'] = 'sys';

        if (! in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerBankAccounts()->create($data);

            return $response;
        } catch (\Throwable $exception) {
            Log::error('[BankAccountObserver][created] '.$exception->getMessage(), $data);
            throw new BankAccountException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws BankAccountException
     */
    public function updated($bankAccount)
    {
        if (! Config::get('iss-supernova.base_uri')) {
            return;
        }

        $bankAccount->refresh();

        $bankAccount->loadMissing(
            'customer',
            'bank',
        );
        if ($bankAccount->customer) {
            $bankAccount->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($bankAccount->customer->user) {
                $bankAccount->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $bankAccount->toArray();
        $data['sync_to'] = 'sys';

        if (! in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerBankAccounts()->update($data);

            return $response;
        } catch (\Throwable $exception) {
            Log::error('[BankAccountObserver][updated] '.$exception->getMessage(), $data);
            throw new BankAccountException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws BankAccountException
     */
    public function deleted($bankAccount)
    {
        $this->updated($bankAccount);
    }
}
