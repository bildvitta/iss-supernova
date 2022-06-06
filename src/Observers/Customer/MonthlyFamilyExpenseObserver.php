<?php

namespace Bildvitta\IssSupernova\Observers\Customer;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class MonthlyFamilyExpenseObserver
{
    public function created($monthlyFamilyExpense)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $monthlyFamilyExpense->loadMissing(
            'customer',
        );
        $data = $monthlyFamilyExpense->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerMonthlyFamilyExpenses()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($monthlyFamilyExpense)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $monthlyFamilyExpense->loadMissing(
            'customer',
        );
        $data = $monthlyFamilyExpense->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerMonthlyFamilyExpenses()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($monthlyFamilyExpense)
    {
        $this->updated($monthlyFamilyExpense);
    }
}
