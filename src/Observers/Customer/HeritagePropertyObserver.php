<?php

namespace Bildvitta\IssSupernova\Observers\Customer;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class HeritagePropertyObserver
{
    public function created($heritageProperty)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $heritageProperty->loadMissing(
            'customer',
            'property_type',
        );
        if ($heritageProperty->customer) {
            $heritageProperty->customer->loadMissing(
                'related_customer'
            );
        }
        $data = $heritageProperty->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerHeritagePropertys()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($heritageProperty)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $heritageProperty->loadMissing(
            'customer',
            'property_type',
        );
        if ($heritageProperty->customer) {
            $heritageProperty->customer->loadMissing(
                'related_customer'
            );
        }
        $data = $heritageProperty->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerHeritagePropertys()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($heritageProperty)
    {
        $this->updated($heritageProperty);
    }
}
