<?php

namespace Bildvitta\IssSupernova\Observers\Customer;

use Bildvitta\IssSupernova\Exceptions\Customer\InformalIncomeException;
use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class InformalIncomeObserver
{
    /**
     * @throws InformalIncomeException
     */
    public function created($informalIncome)
    {
        if (! Config::get('iss-supernova.base_uri')) {
            return;
        }

        $informalIncome->loadMissing(
            'customer',
            'occupation',
        );
        if ($informalIncome->customer) {
            $informalIncome->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($informalIncome->customer->user) {
                $informalIncome->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $informalIncome->toArray();
        $data['sync_to'] = 'sys';

        if (! in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova;
            $response = $issSupernova->customerInformalIncomes()->create($data);

            return $response;
        } catch (\Throwable $exception) {
            Log::error('[InformalIncomeObserver][created] '.$exception->getMessage(), $data);
            throw new InformalIncomeException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws InformalIncomeException
     */
    public function updated($informalIncome)
    {
        if (! Config::get('iss-supernova.base_uri')) {
            return;
        }

        $informalIncome->refresh();

        $informalIncome->loadMissing(
            'customer',
            'occupation',
        );
        if ($informalIncome->customer) {
            $informalIncome->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($informalIncome->customer->user) {
                $informalIncome->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $informalIncome->toArray();
        $data['sync_to'] = 'sys';

        if (! in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova;
            $response = $issSupernova->customerInformalIncomes()->update($data);

            return $response;
        } catch (\Throwable $exception) {
            Log::error('[InformalIncomeObserver][updated] '.$exception->getMessage(), $data);
            throw new InformalIncomeException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws InformalIncomeException
     */
    public function deleted($informalIncome)
    {
        $this->updated($informalIncome);
    }
}
