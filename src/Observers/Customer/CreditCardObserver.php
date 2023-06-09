<?php

namespace Bildvitta\IssSupernova\Observers\Customer;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class CreditCardObserver
{
    public function created($creditCard)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $creditCard->loadMissing(
            'customer',
            'credit_card_flag',
        );
        if ($creditCard->customer) {
            $creditCard->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($creditCard->customer->user) {
                $creditCard->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $creditCard->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerCreditCards()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($creditCard)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $creditCard->refresh();

        $creditCard->loadMissing(
            'customer',
            'credit_card_flag',
        );
        if ($creditCard->customer) {
            $creditCard->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($creditCard->customer->user) {
                $creditCard->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $creditCard->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerCreditCards()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($creditCard)
    {
        $this->updated($creditCard);
    }
}
