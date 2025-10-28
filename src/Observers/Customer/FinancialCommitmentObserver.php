<?php

namespace Bildvitta\IssSupernova\Observers\Customer;

use Bildvitta\IssSupernova\Exceptions\Customer\FinancialCommitmentException;
use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class FinancialCommitmentObserver
{
    /**
     * @throws FinancialCommitmentException
     */
    public function created($financialCommitment)
    {
        if (! Config::get('iss-supernova.base_uri')) {
            return;
        }

        $financialCommitment->loadMissing(
            'customer',
            'financial_commitment',
        );
        if ($financialCommitment->customer) {
            $financialCommitment->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($financialCommitment->customer->user) {
                $financialCommitment->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $financialCommitment->toArray();
        $data['sync_to'] = 'sys';

        if (! in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova;
            $response = $issSupernova->customerFinancialCommitments()->create($data);

            return $response;
        } catch (\Throwable $exception) {
            Log::error('[FinancialCommitmentObserver][created] '.$exception->getMessage(), $data);
            throw new FinancialCommitmentException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws FinancialCommitmentException
     */
    public function updated($financialCommitment)
    {
        if (! Config::get('iss-supernova.base_uri')) {
            return;
        }

        $financialCommitment->refresh();

        $financialCommitment->loadMissing(
            'customer',
            'financial_commitment',
        );
        if ($financialCommitment->customer) {
            $financialCommitment->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($financialCommitment->customer->user) {
                $financialCommitment->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $financialCommitment->toArray();
        $data['sync_to'] = 'sys';

        if (! in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova;
            $response = $issSupernova->customerFinancialCommitments()->update($data);

            return $response;
        } catch (\Throwable $exception) {
            Log::error('[FinancialCommitmentObserver][updated] '.$exception->getMessage(), $data);
            throw new FinancialCommitmentException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws FinancialCommitmentException
     */
    public function deleted($financialCommitment)
    {
        $this->updated($financialCommitment);
    }
}
