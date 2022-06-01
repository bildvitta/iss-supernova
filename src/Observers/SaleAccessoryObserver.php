<?php

namespace Bildvitta\IssSupernova\Observers;

use Bildvitta\IssSupernova\IssSupernova;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class SaleAccessoryObserver
{
    public function created($saleAccessory)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $saleAccessory->loadMissing(
            'category',
            'accessory',
            'sale'
        );
        if ($saleAccessory->sale) {
            $saleAccessory->sale->loadMissing(
                'blueprint',
                'unit'
            );
        }
        $data = $saleAccessory->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->saleAccessories()->create($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function updated($saleAccessory)
    {
        if (!Config::get('iss-supernova.base_uri')) {
            return;
        }

        $saleAccessory->loadMissing(
            'category',
            'accessory',
            'sale'
        );
        if ($saleAccessory->sale) {
            $saleAccessory->sale->loadMissing(
                'blueprint',
                'unit'
            );
        }
        $data = $saleAccessory->toArray();
        $data['sync_to'] = 'sys';

        try {
            $issSupernova = new IssSupernova();
            $response = $issSupernova->saleAccessories()->update($data);
            return $response;
        } catch (\Throwable $exception) {
            Log::error($exception->getMessage());
            throw $exception;
        }
    }

    public function deleted($saleAccessory)
    {
        //
    }
}
