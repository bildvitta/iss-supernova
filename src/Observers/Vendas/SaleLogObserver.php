<?php

namespace Bildvitta\IssSupernova\Observers\Vendas;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class SaleLogObserver
{

    public function created($saleLog)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $saleLog->loadMissing(
            'sale',
            'user',
            'hub_company',
        );
        if ($saleLog->sale) {
            $saleLog->sale->loadMissing(
                'unit'
            );
        }

        $data = $saleLog->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['hub_company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->post('/sale-logs', $data);
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($saleLog)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $saleLog->refresh();

        $saleLog->loadMissing(
            'sale',
            'user',
            'hub_company',
        );
        if ($saleLog->sale) {
            $saleLog->sale->loadMissing(
                'unit'
            );
        }

        $data = $saleLog->toArray();
        $data['sync_to'] = 'sys';

        if (!in_array($data['hub_company']['uuid'], Config::get('iss-supernova.companies'))) {
            return;
        }

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->put('/sale-logs', $data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($saleLog)
    {
        $this->updated($saleLog);
    }
}
