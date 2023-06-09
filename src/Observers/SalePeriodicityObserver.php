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
        if ($salePeriodicity->sale) {
            $salePeriodicity->sale->loadMissing(
                'real_estate_development'
            );

            if ($salePeriodicity->sale->real_estate_development) {
                $salePeriodicity->sale->real_estate_development->loadMissing(
                    'hub_company'
                );
            }
        }

        $data = $salePeriodicity->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['sale']['real_estate_development']['hub_company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

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

        $salePeriodicity->refresh();

        $salePeriodicity->loadMissing(
            'sale'
        );
        if ($salePeriodicity->sale) {
            $salePeriodicity->sale->loadMissing(
                'real_estate_development'
            );

            if ($salePeriodicity->sale->real_estate_development) {
                $salePeriodicity->sale->real_estate_development->loadMissing(
                    'hub_company'
                );
            }
        }

        $data = $salePeriodicity->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['sale']['real_estate_development']['hub_company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

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
        $this->updated($salePeriodicity);
    }
}
