<?php

namespace Bildvitta\IssSupernova\Observers;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class SalePeriodicityObserver
{
    public function created($salePeriodicity)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $salePeriodicity->loadMissing(
            'sale'
        );
        $data = $salePeriodicity->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->salePeriodicities()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($salePeriodicity)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $salePeriodicity->loadMissing(
            'sale'
        );
        $data = $salePeriodicity->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->salePeriodicities()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($salePeriodicity)
    {
        //
    }
}
