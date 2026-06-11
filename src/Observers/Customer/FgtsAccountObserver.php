<?php

namespace Bildvitta\IssSupernova\Observers\Customer;

use Bildvitta\IssSupernova\Exceptions\Customer\FgtsAccountException;
use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class FgtsAccountObserver
{
    /**
     * @throws FgtsAccountException
     */
    public function created($fgtsAccount)
    {
        if (! Config::get('iss-supernova.base_uri')) {
            return;
        }

        $fgtsAccount->loadMissing(
            'customer',
        );
        if ($fgtsAccount->customer) {
            $fgtsAccount->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($fgtsAccount->customer->user) {
                $fgtsAccount->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $fgtsAccount->toArray();
        $data['sync_to'] = 'sys';

        if (! in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerFgtsAccounts()->create($data);

            return $response;
        } catch (\Throwable $exception) {
            Log::error('[FgtsAccountObserver][created] '.$exception->getMessage(), $data);
            throw new FgtsAccountException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws FgtsAccountException
     */
    public function updated($fgtsAccount)
    {
        if (! Config::get('iss-supernova.base_uri')) {
            return;
        }

        $fgtsAccount->refresh();

        $fgtsAccount->loadMissing(
            'customer',
        );
        if ($fgtsAccount->customer) {
            $fgtsAccount->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($fgtsAccount->customer->user) {
                $fgtsAccount->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $fgtsAccount->toArray();
        $data['sync_to'] = 'sys';

        if (! in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerFgtsAccounts()->update($data);

            return $response;
        } catch (\Throwable $exception) {
            Log::error('[FgtsAccountObserver][updated] '.$exception->getMessage(), $data);
            throw new FgtsAccountException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws FgtsAccountException
     */
    public function deleted($fgtsAccount)
    {
        $this->updated($fgtsAccount);
    }
}
