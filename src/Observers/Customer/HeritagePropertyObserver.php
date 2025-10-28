<?php

namespace Bildvitta\IssSupernova\Observers\Customer;

use Bildvitta\IssSupernova\Exceptions\Customer\HeritagePropertyException;
use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class HeritagePropertyObserver
{
    /**
     * @throws HeritagePropertyException
     */
    public function created($heritageProperty)
    {
        if (! Config::get('iss-supernova.base_uri')) {
            return;
        }

        $heritageProperty->loadMissing(
            'customer',
            'property_type',
        );
        if ($heritageProperty->customer) {
            $heritageProperty->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($heritageProperty->customer->user) {
                $heritageProperty->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $heritageProperty->toArray();
        $data['sync_to'] = 'sys';

        if (! in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova;
            $response = $issSupernova->customerHeritagePropertys()->create($data);

            return $response;
        } catch (\Throwable $exception) {
            Log::error('[HeritagePropertyObserver][created] '.$exception->getMessage(), $data);
            throw new HeritagePropertyException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws HeritagePropertyException
     */
    public function updated($heritageProperty)
    {
        if (! Config::get('iss-supernova.base_uri')) {
            return;
        }

        $heritageProperty->refresh();

        $heritageProperty->loadMissing(
            'customer',
            'property_type',
        );
        if ($heritageProperty->customer) {
            $heritageProperty->customer->loadMissing(
                'bonds',
                'bonds_from',
                'user',
            );

            if ($heritageProperty->customer->user) {
                $heritageProperty->customer->user->loadMissing(
                    'company'
                );
            }
        }

        $data = $heritageProperty->toArray();
        $data['sync_to'] = 'sys';

        if (! in_array($data['customer']['user']['company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova;
            $response = $issSupernova->customerHeritagePropertys()->update($data);

            return $response;
        } catch (\Throwable $exception) {
            Log::error('[HeritagePropertyObserver][updated] '.$exception->getMessage(), $data);
            throw new HeritagePropertyException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception
            );
        }
    }

    /**
     * @throws HeritagePropertyException
     */
    public function deleted($heritageProperty)
    {
        $this->updated($heritageProperty);
    }
}
