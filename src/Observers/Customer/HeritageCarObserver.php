<?php

namespace Bildvitta\IssSupernova\Observers\Customer;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class HeritageCarObserver
{
    public function created($heritageCar)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $heritageCar->loadMissing(
            'customer',
            'car_type',
        );
        if ($heritageCar->customer) {
            $heritageCar->customer->loadMissing(
                'related_customer'
            );
        }
        $data = $heritageCar->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerHeritageCars()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($heritageCar)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $heritageCar->loadMissing(
            'customer',
            'car_type',
        );
        if ($heritageCar->customer) {
            $heritageCar->customer->loadMissing(
                'related_customer'
            );
        }
        $data = $heritageCar->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->customerHeritageCars()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($heritageCar)
    {
        $this->updated($heritageCar);
    }
}
