<?php

namespace Bildvitta\IssSupernova\Observers\Customer;

use Bildvitta\IssSupernova\Exceptions\Customer\InvestmentException;
use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class InvestmentObserver
{
    /**
     * @throws InvestmentException
     */
    public function created($investment)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $investment->loadMissing(
            'customer',
            'bank',
        );
        if ($investment->customer) {
            $investment->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($investment->customer->user) {
                $investment->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $investment->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerInvestments()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error("[InvestmentObserver][created] " . $exception->getMessage(), $data);
            throw new InvestmentException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws InvestmentException
     */
    public function updated($investment)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $investment->refresh();

        $investment->loadMissing(
            'customer',
            'bank',
        );
        if ($investment->customer) {
            $investment->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($investment->customer->user) {
                $investment->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $investment->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerInvestments()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error("[InvestmentObserver][updated] " . $exception->getMessage(), $data);
            throw new InvestmentException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws InvestmentException
     */
    public function deleted($investment)
    {
        $this->updated($investment);
    }
}
