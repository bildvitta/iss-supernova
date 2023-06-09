<?php

namespace Bildvitta\IssSupernova\Observers\Customer;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class FormalIncomeObserver
{
    public function created($formalIncome)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $formalIncome->loadMissing(
            'customer',
            'occupation',
            'proof_of_income_type',
        );
        if ($formalIncome->customer) {
            $formalIncome->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($formalIncome->customer->user) {
                $formalIncome->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $formalIncome->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerFormalIncomes()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($formalIncome)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $formalIncome->refresh();

        $formalIncome->loadMissing(
            'customer',
            'occupation',
            'proof_of_income_type',
        );
        if ($formalIncome->customer) {
            $formalIncome->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($formalIncome->customer->user) {
                $formalIncome->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $formalIncome->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerFormalIncomes()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($formalIncome)
    {
        $this->updated($formalIncome);
    }
}
